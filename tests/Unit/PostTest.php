<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_slug_is_generated_from_title(): void
    {
        $post = Post::factory()->create([
            'title' => 'Mi Primer Post',
            'slug' => '',
        ]);

        $this->assertEquals('mi-primer-post', $post->slug);
    }

    public function test_tag_list_returns_array(): void
    {
        $post = Post::factory()->create();
        $post->tags()->create(['tag' => 'laravel']);
        $post->tags()->create(['tag' => 'php']);

        $tagList = $post->tag_list;

        $this->assertIsArray($tagList);
        $this->assertContains('laravel', $tagList);
        $this->assertContains('php', $tagList);
        $this->assertCount(2, $tagList);
    }

    public function test_published_scope_excludes_drafts(): void
    {
        $user = User::factory()->create();
        Post::factory()->published()->count(2)->create(['user_id' => $user->id]);
        Post::factory()->draft()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(2, Post::published()->get());
    }

    public function test_published_scope_orders_by_published_at_desc(): void
    {
        $user = User::factory()->create();
        $oldest = Post::factory()->create([
            'user_id' => $user->id,
            'published' => true,
            'published_at' => now()->subDays(20),
        ]);
        $newest = Post::factory()->create([
            'user_id' => $user->id,
            'published' => true,
            'published_at' => now()->subDay(),
        ]);
        $middle = Post::factory()->create([
            'user_id' => $user->id,
            'published' => true,
            'published_at' => now()->subDays(10),
        ]);

        $ordered = Post::published()->pluck('id')->all();

        $this->assertSame([$newest->id, $middle->id, $oldest->id], $ordered);
    }

    public function test_latest_published_scope_respects_limit(): void
    {
        $user = User::factory()->create();
        Post::factory()->published()->count(5)->create(['user_id' => $user->id]);

        $this->assertCount(3, Post::latestPublished(3)->get());
    }
}
