<?php

namespace Tests\Feature;

use App\Models\Thread;
use App\Models\ThreadComment;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreadCommentAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_comment_owner_can_update_and_delete_own_comment(): void
    {
        // Ensure roles exist because UserFactory assigns a role after creating
        $guard = config('auth.defaults.guard') ?? 'web';
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
        Role::firstOrCreate(['name' => 'author', 'guard_name' => $guard]);

        $user = User::factory()->create();
        // create thread with required fields
        $thread = Thread::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Thread',
            'slug' => 'test-thread-' . rand(1000, 9999),
            'body' => '<p>Body</p>',
        ]);

        $comment = ThreadComment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => 'Original',
        ]);

        // Owner can update
        $this->actingAs($user)
            ->putJson(route('community.comments.update', [$thread->id, $comment->id]), [
                'body' => 'Updated by owner',
            ])
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('thread_comments', [
            'id' => $comment->id,
            'body' => 'Updated by owner',
        ]);

        // Owner can delete
        $this->actingAs($user)
            ->deleteJson(route('community.comments.destroy', [$thread->id, $comment->id]))
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('thread_comments', ['id' => $comment->id]);
    }

    public function test_admin_cannot_update_others_but_can_delete(): void
    {
        // Ensure roles exist because UserFactory assigns a role after creating
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'author']);

        $owner = User::factory()->create();
        $admin = User::factory()->create();
        // ensure the admin user has only the admin role
        $admin->syncRoles('admin');
        $owner->syncRoles('author');

        $thread = Thread::factory()->create([
            'user_id' => $owner->id,
            'title' => 'Owner Thread',
            'slug' => 'owner-thread-' . rand(1000, 9999),
            'body' => '<p>Content</p>',
        ]);

        $comment = ThreadComment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $owner->id,
            'body' => 'Owner comment',
        ]);

        // Admin cannot update other's comment (forbidden or 403)
        $this->actingAs($admin)
            ->putJson(route('community.comments.update', [$thread->id, $comment->id]), [
                'body' => 'Updated by admin',
            ])
            ->assertStatus(403);

        // Admin can delete
        $this->actingAs($admin)
            ->deleteJson(route('community.comments.destroy', [$thread->id, $comment->id]))
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('thread_comments', ['id' => $comment->id]);
    }
}
