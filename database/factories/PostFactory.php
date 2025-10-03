<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
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
        $title = fake()->sentence(15);

        return [
            'title' => $title,
            'slug' => str()->slug($title) . '-' . str()->random(5),
            'image' => 'https://picsum.photos/800/600?random=' . uniqid(),
            'image_caption' => fake()->sentence(),
            'content' => fake()->paragraphs(15, true),
            'min_read' => fake()->numberBetween(1, 10),
            'teaser' => fake()->optional()->text(150),
            'status' => fake()->randomElement(['draft', 'pending', 'published', 'rejected', 'archived']),
            'rejected_reason' => fake()->optional()->sentence(),

            'views_count' => fake()->numberBetween(0, 5000),

            'user_id' => User::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,

            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($post) {
            $tagIds = Category::inRandomOrder()->limit(3)->pluck('id')->toArray();

            $post->tags()->attach($tagIds);
        });
    }
}
