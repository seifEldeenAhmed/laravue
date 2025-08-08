<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_post(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/posts', [
                'title' => 'Test Post',
                'content' => 'This is a test post content.',
                'status' => 'draft'
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'post' => [
                        'id',
                        'title',
                        'content',
                        'status',
                        'author',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post',
            'content' => 'This is a test post content.',
            'author_id' => $user->id,
            'author_type' => User::class
        ]);
    }

    public function test_user_can_only_view_own_posts(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $post1 = Post::factory()->create([
            'author_id' => $user1->id,
            'author_type' => User::class
        ]);

        $post2 = Post::factory()->create([
            'author_id' => $user2->id,
            'author_type' => User::class
        ]);

        $response = $this->actingAs($user1, 'sanctum')
            ->getJson('/api/posts/' . $post2->id);

        $response->assertStatus(403);
    }

    public function test_admin_can_view_all_posts(): void
    {
        $admin = \App\Models\Admin::factory()->create();
        $user = User::factory()->create();

        $post = Post::factory()->create([
            'author_id' => $user->id,
            'author_type' => User::class
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/posts/' . $post->id);

        $response->assertStatus(200);
    }

    public function test_post_validation_rules(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/posts', [
                'title' => '', // Required field empty
                'content' => '',
                'status' => 'invalid_status'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content', 'status']);
    }
}
