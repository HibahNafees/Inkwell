<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_visitor_can_register_and_is_logged_in(): void
    {
        $response = $this->post('/register', [
            'name' => 'Ada Writer',
            'username' => 'ada',
            'email' => 'ada@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['username' => 'ada']);
    }

    public function test_a_user_can_log_in_and_out(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_guests_cannot_write_posts(): void
    {
        $this->get(route('posts.create'))->assertRedirect(route('login'));
        $this->post(route('posts.store'), ['title' => 'x', 'body' => 'y', 'action' => 'publish'])
            ->assertRedirect(route('login'));
    }

    public function test_a_user_can_publish_a_post_that_appears_on_the_feed_and_their_blog(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('posts.store'), [
            'title' => 'Hello World',
            'body' => 'My first story.',
            'action' => 'publish',
        ])->assertRedirect();

        $post = Post::firstOrFail();
        $this->assertTrue($post->isPublished());

        $this->get(route('home'))->assertOk()->assertSee('Hello World');
        $this->get(route('authors.show', $user))->assertOk()->assertSee('Hello World');
        $this->get(route('posts.show', $post))->assertOk()->assertSee('My first story.');
    }

    public function test_drafts_are_private_to_their_author(): void
    {
        $post = Post::factory()->draft()->create(['title' => 'Secret draft']);

        $this->get(route('home'))->assertDontSee('Secret draft');
        $this->get(route('posts.show', $post))->assertForbidden();
        $this->actingAs(User::factory()->create())->get(route('posts.show', $post))->assertForbidden();
        $this->actingAs($post->author)->get(route('posts.show', $post))->assertOk()->assertSee('Secret draft');
    }

    public function test_only_the_author_can_edit_or_delete_a_post(): void
    {
        $post = Post::factory()->create();
        $stranger = User::factory()->create();

        $this->actingAs($stranger)->get(route('posts.edit', $post))->assertForbidden();
        $this->actingAs($stranger)->delete(route('posts.destroy', $post))->assertForbidden();

        $this->actingAs($post->author)->put(route('posts.update', $post), [
            'title' => 'Updated title',
            'body' => 'Updated body',
            'action' => 'publish',
        ])->assertRedirect();
        $this->assertSame('Updated title', $post->fresh()->title);

        $this->actingAs($post->author)->delete(route('posts.destroy', $post))->assertRedirect(route('dashboard'));
        $this->assertModelMissing($post);
    }

    public function test_logged_in_users_can_comment_on_published_posts(): void
    {
        $post = Post::factory()->create();
        $reader = User::factory()->create();

        $this->post(route('comments.store', $post), ['body' => 'Anon'])->assertRedirect(route('login'));

        $this->actingAs($reader)->post(route('comments.store', $post), ['body' => 'Great read!'])->assertRedirect();

        $this->assertDatabaseHas('comments', ['post_id' => $post->id, 'user_id' => $reader->id, 'body' => 'Great read!']);
        $this->get(route('posts.show', $post))->assertSee('Great read!');
    }

    public function test_drafts_cannot_be_commented_on(): void
    {
        $post = Post::factory()->draft()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('comments.store', $post), ['body' => 'Sneaky'])
            ->assertForbidden();
    }

    public function test_comments_can_be_deleted_by_their_writer_or_the_post_author_only(): void
    {
        $comment = Comment::factory()->create();

        $this->actingAs(User::factory()->create())->delete(route('comments.destroy', $comment))->assertForbidden();
        $this->actingAs($comment->post->author)->delete(route('comments.destroy', $comment))->assertRedirect();
        $this->assertModelMissing($comment);

        $second = Comment::factory()->create();
        $this->actingAs($second->author)->delete(route('comments.destroy', $second))->assertRedirect();
        $this->assertModelMissing($second);
    }

    public function test_comment_bodies_are_escaped(): void
    {
        $comment = Comment::factory()->create(['body' => '<script>alert(1)</script>']);

        $this->get(route('posts.show', $comment->post))
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;', false);
    }
}
