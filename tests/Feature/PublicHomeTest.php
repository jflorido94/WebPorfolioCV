<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicHomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_renders_user_data(): void
    {
        $user = User::factory()->admin()->create(['name' => 'Test Admin']);
        Profile::factory()->create([
            'user_id' => $user->id,
            'title' => 'Software Engineer',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Test Admin');
        $response->assertSee('Software Engineer');
    }

    public function test_home_shows_only_three_latest_posts(): void
    {
        $user = User::factory()->admin()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        Post::factory()->published()->count(5)->create(['user_id' => $user->id]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $posts = $response->viewData('posts');
        $this->assertCount(3, $posts);
    }

    public function test_home_renders_without_profile(): void
    {
        User::factory()->admin()->create();

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
