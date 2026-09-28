<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
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

        $authors->each(function (User $author) use ($authors): void {
            Post::factory(3)->for($author, 'author')->create()->each(function (Post $post) use ($authors): void {
                Comment::factory(2)->for($post)->for($authors->random(), 'author')->create();
            });

            Post::factory()->draft()->for($author, 'author')->create();
        });
    }
}
