<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlogFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_like_and_unlike_a_post(): void
    {
        $post = Post::factory()->create();
        $reader = User::factory()->create();

        $this->actingAs($reader)->post(route('posts.like', $post))->assertRedirect();
        $this->assertTrue($post->isLikedBy($reader));
        $this->get(route('posts.show', $post))->assertSee('Liked · 1');

        $this->actingAs($reader)->post(route('posts.like', $post))->assertRedirect();
        $this->assertFalse($post->isLikedBy($reader));
    }

    public function test_guests_cannot_like_and_drafts_cannot_be_liked(): void
    {
        $post = Post::factory()->create();
        $this->post(route('posts.like', $post))->assertRedirect(route('login'));

        $draft = Post::factory()->draft()->create();
        $this->actingAs(User::factory()->create())->post(route('posts.like', $draft))->assertForbidden();
    }

    public function test_liked_posts_page_lists_what_the_user_liked(): void
    {
        $reader = User::factory()->create();
        $liked = Post::factory()->create(['title' => 'Loved this one']);
        Post::factory()->create(['title' => 'Never liked']);
        $liked->likers()->attach($reader);

        $this->actingAs($reader)->get(route('likes.index'))
            ->assertOk()
            ->assertSee('Loved this one')
            ->assertDontSee('Never liked');
    }

    public function test_popular_sort_orders_posts_by_likes(): void
    {
        $quiet = Post::factory()->create(['title' => 'Quiet post', 'published_at' => now()->subHour()]);
        $popular = Post::factory()->create(['title' => 'Popular post', 'published_at' => now()->subDays(5)]);
        $popular->likers()->attach(User::factory(3)->create());

        $this->get(route('home', ['sort' => 'popular']))->assertSeeInOrder(['Popular post', 'Quiet post']);
        $this->get(route('home'))->assertSeeInOrder(['Quiet post', 'Popular post']);
    }

    public function test_posts_can_be_tagged_and_browsed_by_tag(): void
    {
        $author = User::factory()->create();

        $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Trip to Lahore',
            'body' => 'Great food.',
            'tags' => 'Travel, food ,travel,,  ',
            'action' => 'publish',
        ]);

        $post = Post::firstOrFail();
        $this->assertEqualsCanonicalizing(['travel', 'food'], $post->tags->pluck('name')->all());

        $this->get(route('tags.show', Tag::where('slug', 'travel')->firstOrFail()))
            ->assertOk()
            ->assertSee('Trip to Lahore');
        $this->get(route('home', ['q' => 'food']))->assertSee('Trip to Lahore');
    }

    public function test_tags_are_capped_per_post(): void
    {
        $this->assertCount(Tag::MAX_PER_POST, Tag::namesFromInput('a,b,c,d,e,f,g'));
    }

    public function test_a_cover_image_can_be_uploaded_replaced_and_is_removed_with_the_post(): void
    {
        Storage::fake('public');
        $author = User::factory()->create();

        $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'With cover',
            'body' => 'Body',
            'cover' => UploadedFile::fake()->image('cover.jpg', 800, 400),
            'action' => 'publish',
        ]);

        $post = Post::firstOrFail();
        $firstCover = $post->cover_image;
        Storage::disk('public')->assertExists($firstCover);

        $this->actingAs($author)->put(route('posts.update', $post), [
            'title' => 'With cover',
            'body' => 'Body',
            'cover' => UploadedFile::fake()->image('new.png', 800, 400),
            'action' => 'publish',
        ]);

        Storage::disk('public')->assertMissing($firstCover);
        $secondCover = $post->fresh()->cover_image;
        Storage::disk('public')->assertExists($secondCover);

        $this->actingAs($author)->delete(route('posts.destroy', $post));
        Storage::disk('public')->assertMissing($secondCover);
    }

    public function test_non_image_covers_are_rejected(): void
    {
        $this->actingAs(User::factory()->create())->post(route('posts.store'), [
            'title' => 'Bad cover',
            'body' => 'Body',
            'cover' => UploadedFile::fake()->create('evil.php', 10, 'application/x-php'),
            'action' => 'publish',
        ])->assertSessionHasErrors('cover');

        $this->assertDatabaseCount('posts', 0);
    }

    public function test_post_bodies_render_markdown_but_escape_html(): void
    {
        $post = Post::factory()->create([
            'body' => "## Heading\n\n**bold** <script>alert(1)</script> [x](javascript:alert(1))",
        ]);

        $this->get(route('posts.show', $post))
            ->assertSee('<h2>Heading</h2>', false)
            ->assertSee('<strong>bold</strong>', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('href="javascript:', false);
    }

    public function test_a_user_can_change_their_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'wrong',
            'password' => 'new-password-1',
            'password_confirmation' => 'new-password-1',
        ])->assertSessionHasErrorsIn('password', 'current_password');

        $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'password',
            'password' => 'new-password-1',
            'password_confirmation' => 'new-password-1',
        ])->assertRedirect(route('profile.edit'));

        $this->assertTrue(Hash::check('new-password-1', $user->fresh()->password));
    }

    public function test_post_page_shows_more_from_the_author(): void
    {
        $post = Post::factory()->create();
        Post::factory()->for($post->author, 'author')->create(['title' => 'Another story']);

        $this->get(route('posts.show', $post))->assertSee('More from')->assertSee('Another story');
    }
}
