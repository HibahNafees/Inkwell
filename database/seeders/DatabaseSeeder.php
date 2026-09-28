<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with demo authors, posts and comments.
     */
    public function run(): void
    {
        $authors = User::factory(4)->create();

        User::factory()->create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
        ]);

        $tags = collect(['travel', 'food', 'tech', 'life', 'books'])
            ->map(fn (string $name) => Tag::create(['name' => $name, 'slug' => $name]));

        $authors->each(function (User $author) use ($authors, $tags): void {
            Post::factory(3)->for($author, 'author')->create()->each(function (Post $post) use ($authors, $tags): void {
                Comment::factory(2)->for($post)->for($authors->random(), 'author')->create();
                $post->tags()->attach($tags->random(2));
                $post->likers()->attach($authors->random(rand(0, 3)));
            });

            Post::factory()->draft()->for($author, 'author')->create();
        });
    }
}
