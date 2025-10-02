<?php

namespace Tests\Feature;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostLikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_like_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('posts.like', $post));

        $response->assertStatus(200);
        $response->assertJson([
            'liked' => true,
            'likes_count' => 1,
        ]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'likeable_type' => Post::class,
            'likeable_id' => $post->id,
        ]);
    }

    public function test_user_can_unlike_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        // First like the post
        $post->likes()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->postJson(route('posts.like', $post));

        $response->assertStatus(200);
        $response->assertJson([
            'liked' => false,
            'likes_count' => 0,
        ]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'likeable_type' => Post::class,
            'likeable_id' => $post->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_like_post(): void
    {
        $post = Post::factory()->create();

        $response = $this->postJson(route('posts.like', $post));

        $response->assertStatus(401);
    }

    public function test_post_shows_correct_like_count_in_index(): void
    {
        $post = Post::factory()->create();
        $users = User::factory()->count(3)->create();

        // Create likes for the post
        foreach ($users as $user) {
            $post->likes()->create(['user_id' => $user->id]);
        }

        $response = $this->get(route('posts.index'));

        $response->assertStatus(200);
        $response->assertSee('3'); // Should show likes count
    }
}
