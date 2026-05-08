<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_returns_valid_xml(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertSame('application/xml', $response->headers->get('content-type'));

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml, 'Sitemap is not valid XML');
    }

    public function test_sitemap_includes_static_routes(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertSee('/cv', false);
        $response->assertSee('/blog', false);
    }

    public function test_sitemap_includes_published_posts_and_excludes_drafts(): void
    {
        $user = User::factory()->admin()->create();
        $published = Post::factory()->published()->create([
            'user_id' => $user->id,
            'slug' => 'visible-post',
        ]);
        $draft = Post::factory()->draft()->create([
            'user_id' => $user->id,
            'slug' => 'hidden-draft',
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertSee('/blog/visible-post', false);
        $response->assertDontSee('/blog/hidden-draft', false);
    }
}
