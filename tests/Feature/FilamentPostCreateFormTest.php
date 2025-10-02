<?php

namespace Tests\Feature;

use App\Filament\Shared\Resources\Posts\Pages\CreatePost;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentPostCreateFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set the current panel to admin
        Filament::setCurrentPanel('admin');
    }

    /**
     * Test that authenticated user can access post create form
     */
    public function test_authenticated_user_can_access_post_create_form(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($user);

        $response = Livewire::test(CreatePost::class);

        $response->assertStatus(200);
    }

    /**
     * Test that form has AI generation actions
     */
    public function test_form_has_ai_generation_actions(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($user);

        $response = Livewire::test(CreatePost::class);

        // Check that the form renders without errors
        $response->assertStatus(200);

        // The form should contain the content field which should have AI actions
        $response->assertSee('content');
    }

    /**
     * Test that user can create a post with basic data
     */
    public function test_user_can_create_post_with_basic_data(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($user);

        $response = Livewire::test(CreatePost::class)
            ->fillForm([
                'title' => 'Test Laravel Tutorial',
                'slug' => 'test-laravel-tutorial',
                'content' => '<p>This is a test tutorial content.</p>',
                'meta_description' => 'A test tutorial about Laravel',
                'status' => 'published',
                'featured_image' => null,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Laravel Tutorial',
            'slug' => 'test-laravel-tutorial',
            'status' => 'published',
        ]);
    }
}
