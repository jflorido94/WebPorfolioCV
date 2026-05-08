<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic smoke test — checks the blog index renders without errors.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/blog');

        $response->assertStatus(200);
    }
}
