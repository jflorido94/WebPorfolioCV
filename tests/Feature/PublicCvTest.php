<?php

namespace Tests\Feature;

use Database\Seeders\TestCvSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCvTest extends TestCase
{
    use RefreshDatabase;

    private function seedFullCv(): void
    {
        $this->seed(TestCvSeeder::class);
    }

    public function test_cv_show_renders_with_user_data(): void
    {
        $this->seedFullCv();

        $response = $this->get(route('cv.show'));

        $response->assertStatus(200);
        $response->assertSee(TestCvSeeder::USER_NAME);
        $response->assertSee(TestCvSeeder::PROFILE_TITLE);
    }

    public function test_cv_print_renders(): void
    {
        $this->seedFullCv();

        $response = $this->get(route('cv.print'));

        $response->assertStatus(200);
    }

    public function test_cv_show_returns_404_when_no_user_exists(): void
    {
        $response = $this->get(route('cv.show'));

        $response->assertStatus(404);
    }

    public function test_cv_download_pdf_returns_pdf_download(): void
    {
        $this->seedFullCv();

        $response = $this->get(route('cv.download-pdf'));

        $response->assertStatus(200);
        $this->assertStringStartsWith('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString('attachment', (string) $response->headers->get('content-disposition'));
        $this->assertStringContainsString('cv_', (string) $response->headers->get('content-disposition'));
    }
}
