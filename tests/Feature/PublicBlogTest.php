<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicBlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_index_shows_published_posts(): void
    {
        $admin = User::factory()->admin()->create();

        Post::factory()->published()->create(['user_id' => $admin->id]);
        Post::factory()->published()->create(['user_id' => $admin->id]);
        Post::factory()->published()->create(['user_id' => $admin->id]);
        Post::factory()->draft()->create(['user_id' => $admin->id]);
        Post::factory()->draft()->create(['user_id' => $admin->id]);

        $response = $this->get('/blog');

        $response->assertStatus(200);
        $response->assertViewHas('posts');

        $posts = $response->viewData('posts');
        $this->assertCount(3, $posts);
    }

    public function test_draft_post_is_not_publicly_accessible(): void
    {
        $admin = User::factory()->admin()->create();
        $draftPost = Post::factory()->draft()->create(['user_id' => $admin->id]);

        $response = $this->get("/blog/{$draftPost->slug}");

        $response->assertStatus(404);
    }

    public function test_published_post_is_accessible_by_slug(): void
    {
        $admin = User::factory()->admin()->create();
        $publishedPost = Post::factory()->published()->create(['user_id' => $admin->id]);

        $response = $this->get("/blog/{$publishedPost->slug}");

        $response->assertStatus(200);
        $response->assertViewHas('post');
        $response->assertSee($publishedPost->title);
    }
}
