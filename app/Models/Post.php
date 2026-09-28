<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

#[Fillable(['title', 'excerpt', 'body', 'cover_image', 'published_at'])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Post $post): void {
            $post->slug ??= static::uniqueSlugFor($post->title);
        });

        static::deleted(function (Post $post): void {
            if ($post->cover_image) {
                Storage::disk('public')->delete($post->cover_image);
            }
        });
    }

    public static function uniqueSlugFor(string $title): string
    {
        $base = Str::slug($title) ?: 'post';

        return $base.'-'.Str::lower(Str::random(6));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->orderBy('name');
    }

    /**
     * Users who liked this post.
     *
     * @return BelongsToMany<User, $this>
     */
    public function likers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'likes')->withPivot('created_at');
    }

    /**
     * @param  Builder<Post>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /**
     * Eager-load everything a post card needs.
     *
     * @param  Builder<Post>  $query
     */
    public function scopeForCards(Builder $query): void
    {
        $query->with(['author', 'tags'])->withCount(['comments', 'likers']);
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }

    public function isLikedBy(?User $user): bool
    {
        return $user !== null && $this->likers()->whereKey($user->id)->exists();
    }

    /**
     * The post body rendered from Markdown, with raw HTML escaped.
     */
    public function renderedBody(): HtmlString
    {
        return new HtmlString(Str::markdown($this->body, [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]));
    }

    public function summary(): string
    {
        return $this->excerpt ?: Str::limit(trim(strip_tags(Str::markdown($this->body, ['html_input' => 'strip']))), 180);
    }

    public function readingMinutes(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($this->body)) / 220));
    }

    public function coverUrl(): ?string
    {
        return $this->cover_image ? asset('storage/'.$this->cover_image) : null;
    }

    /**
     * Replace the post's tags from a comma-separated string.
     */
    public function syncTagsFrom(?string $input): void
    {
        $this->tags()->sync(Tag::idsFromInput($input));
    }
}
