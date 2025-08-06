<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $title = $this->faker->sentence(6);

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'image' => 'https://picsum.photos/800/600?random=' . uniqid(),
            'image_caption' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(15, true),
            'min_read' => $this->faker->numberBetween(1, 10),
            'teaser' => $this->faker->optional()->text(150),
            'status' => $this->faker->randomElement(['draft', 'pending', 'published', 'rejected', 'archived']),
            'rejected_reason' => $this->faker->optional()->sentence(),

            'views_count' => $this->faker->numberBetween(0, 5000),

            'user_id' => User::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,

            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),

        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Post $post) {
            $tagIds = Tag::inRandomOrder()->limit(rand(1, 5))->pluck('id');

            $post->tags()->attach($tagIds);
        });
    }
}
