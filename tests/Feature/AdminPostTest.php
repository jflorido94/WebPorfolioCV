<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin(): void
    {
        $response = $this->get('/admin/posts');

        $response->assertRedirect('/login');
    }

    public function test_admin_can_create_post(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/posts', [
            'title' => 'Mi Nuevo Post',
            'summary' => 'Resumen del post',
            'content' => '# Contenido del post',
            'category' => 'blog',
            'tags' => 'laravel,php',
            'publish' => true,
        ]);

        $response->assertRedirect(route('admin.posts.index'));
        $this->assertDatabaseHas('posts', [
            'title' => 'Mi Nuevo Post',
            'slug' => 'mi-nuevo-post',
            'published' => true,
        ]);
    }

    public function test_admin_can_delete_own_post(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create(['user_id' => $admin->id]);

        $response = $this->actingAs($admin)->delete(route('admin.posts.destroy', $post));

        $response->assertRedirect(route('admin.posts.index'));
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_admin_cannot_delete_another_users_post(): void
    {
        $admin1 = User::factory()->admin()->create();
        $admin2 = User::factory()->admin()->create();
        $post = Post::factory()->create(['user_id' => $admin1->id]);

        $response = $this->actingAs($admin2)->delete(route('admin.posts.destroy', $post));

        $response->assertStatus(403);
        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    public function test_store_post_requires_title_and_content(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/posts', [
            'title' => '',
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'content']);
    }
}
