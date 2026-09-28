<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $title = rtrim(fake()->sentence(6), '.'),
            'slug' => Post::uniqueSlugFor($title),
            'excerpt' => fake()->sentence(18),
            'body' => implode("\n\n", fake()->paragraphs(5)),
            'published_at' => fake()->dateTimeBetween('-2 months', '-1 hour'),
        ];
    }

    /**
     * Indicate that the post is an unpublished draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => null,
        ]);
    }
}
