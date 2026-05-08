<?php

namespace Tests\Feature;

use App\Jobs\EnrichPageMetric;
use App\Models\PageMetric;
use App\Models\Profile;
use App\Models\User;
use App\Services\GeoLookupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MetricsTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->admin()->create();
        Profile::factory()->create(['user_id' => $user->id]);
    }

    // ── Middleware: recording visits ────────────────────────────────────────

    public function test_visiting_home_creates_page_metric(): void
    {
        $this->get('/');

        $this->assertDatabaseHas('page_metrics', ['page' => 'home']);
    }

    public function test_visiting_cv_creates_page_metric(): void
    {
        $this->get('/cv');

        $this->assertDatabaseHas('page_metrics', ['page' => 'cv']);
    }

    public function test_blog_routes_are_not_tracked(): void
    {
        $this->get('/blog');

        $this->assertDatabaseCount('page_metrics', 0);
    }

    public function test_same_ip_and_page_is_only_recorded_once_per_day(): void
    {
        $this->get('/');
        $this->get('/');
        $this->get('/');

        $this->assertDatabaseCount('page_metrics', 1);
    }

    public function test_same_ip_on_different_pages_creates_separate_records(): void
    {
        $this->get('/');
        $this->get('/cv');

        $this->assertDatabaseCount('page_metrics', 2);
    }

    public function test_same_ip_on_different_days_creates_separate_records(): void
    {
        PageMetric::factory()->create([
            'ip'         => '127.0.0.1',
            'page'       => 'home',
            'created_at' => now()->subDay(),
        ]);

        $this->get('/');

        $this->assertDatabaseCount('page_metrics', 2);
    }

    public function test_authenticated_user_visit_is_not_tracked(): void
    {
        $admin = User::first();

        $this->actingAs($admin)->get('/');

        $this->assertDatabaseCount('page_metrics', 0);
    }

    public function test_bot_user_agent_is_not_tracked(): void
    {
        $this->withHeader('User-Agent', 'Googlebot/2.1 (+http://www.google.com/bot.html)')
             ->get('/');

        $this->assertDatabaseCount('page_metrics', 0);
    }

    public function test_metric_stores_correct_page_and_ip(): void
    {
        $this->get('/cv');

        $metric = PageMetric::first();
        $this->assertNotNull($metric);
        $this->assertSame('cv', $metric->page);
        $this->assertNotEmpty($metric->ip);
    }

    public function test_cloudflare_connecting_ip_header_is_used_when_present(): void
    {
        $this->withHeader('CF-Connecting-IP', '1.2.3.4')->get('/');

        $metric = PageMetric::first();
        $this->assertNotNull($metric);
        $this->assertSame('1.2.3.4', $metric->ip);
    }

    public function test_deduplication_uses_cloudflare_ip(): void
    {
        $this->withHeader('CF-Connecting-IP', '1.2.3.4')->get('/');
        $this->withHeader('CF-Connecting-IP', '1.2.3.4')->get('/');

        $this->assertDatabaseCount('page_metrics', 1);
    }

    // ── Job dispatching ─────────────────────────────────────────────────────

    public function test_enrich_job_is_dispatched_after_visit(): void
    {
        Queue::fake();

        $this->get('/');

        Queue::assertPushed(EnrichPageMetric::class);
    }

    // ── GeoLookupService via job ─────────────────────────────────────────────

    public function test_enrich_job_sets_geo_resolved_true_for_non_public_ip(): void
    {
        $this->get('/');

        $metric = PageMetric::first();
        $this->assertNotNull($metric);

        $metric->refresh();
        $this->assertTrue($metric->geo_resolved);
        $this->assertNull($metric->country);
        $this->assertNull($metric->city);
        $this->assertNull($metric->isp);
    }

    public function test_enrich_job_saves_geo_data_from_service(): void
    {
        $this->mock(GeoLookupService::class, function ($mock) {
            $mock->shouldReceive('lookup')->andReturn([
                'country' => 'Spain',
                'city'    => 'Madrid',
                'isp'     => 'Telefonica',
            ]);
        });

        $this->get('/');

        $metric = PageMetric::first();
        $this->assertNotNull($metric);

        $metric->refresh();
        $this->assertTrue($metric->geo_resolved);
        $this->assertSame('Spain', $metric->country);
        $this->assertSame('Madrid', $metric->city);
        $this->assertSame('Telefonica', $metric->isp);
    }

    public function test_enrich_job_handles_api_failure_gracefully(): void
    {
        $this->mock(GeoLookupService::class, function ($mock) {
            $mock->shouldReceive('lookup')->andReturn(null);
        });

        $this->get('/');

        $metric = PageMetric::first();
        $this->assertNotNull($metric);

        $metric->refresh();
        $this->assertTrue($metric->geo_resolved);
        $this->assertNull($metric->country);
    }

    // ── Admin controller ─────────────────────────────────────────────────────

    public function test_admin_can_view_metrics_index(): void
    {
        $admin = User::first();
        PageMetric::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('admin.metrics.index'));

        $response->assertStatus(200);
        $response->assertViewHas('metrics');
    }

    public function test_guest_cannot_view_metrics_index(): void
    {
        $response = $this->get(route('admin.metrics.index'));

        $response->assertRedirect('/login');
    }

    public function test_non_admin_cannot_view_metrics_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.metrics.index'));

        $response->assertStatus(403);
    }

    public function test_metrics_are_paginated_latest_first(): void
    {
        $admin = User::first();

        PageMetric::factory()->create(['created_at' => now()->subDays(2), 'page' => 'cv']);
        PageMetric::factory()->create(['created_at' => now()->subDay(),   'page' => 'cv']);
        PageMetric::factory()->create(['created_at' => now(),             'page' => 'home']);

        $response = $this->actingAs($admin)->get(route('admin.metrics.index'));

        $metrics = $response->viewData('metrics');
        $this->assertSame('home', $metrics->first()->page);
        $this->assertSame('cv',   $metrics->last()->page);
    }
}
