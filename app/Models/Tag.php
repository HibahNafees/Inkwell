<?php

namespace App\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug'])]
class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    public const MAX_PER_POST = 5;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return BelongsToMany<Post, $this>
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    /**
     * Split a comma-separated tag string into clean, unique names.
     *
     * @return list<string>
     */
    public static function namesFromInput(?string $input): array
    {
        return collect(explode(',', (string) $input))
            ->map(fn (string $name) => Str::of($name)->squish()->lower()->limit(30, '')->toString())
            ->filter(fn (string $name) => Str::slug($name) !== '')
            ->unique(fn (string $name) => Str::slug($name))
            ->take(self::MAX_PER_POST)
            ->values()
            ->all();
    }

    /**
     * Find or create tags for the given input and return their ids.
     *
     * @return list<int>
     */
    public static function idsFromInput(?string $input): array
    {
        return collect(self::namesFromInput($input))
            ->map(fn (string $name) => self::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name])->id)
            ->all();
    }
}
