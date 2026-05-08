<?php

namespace Tests\Feature;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCvTest extends TestCase
{
    use RefreshDatabase;

    private function seedFullCv(): User
    {
        $user = User::factory()->admin()->create(['name' => 'CV User']);
        Profile::factory()->create(['user_id' => $user->id, 'title' => 'CV Title']);
        Experience::factory()->count(2)->create(['user_id' => $user->id]);
        Education::factory()->create(['user_id' => $user->id]);
        Skill::factory()->count(3)->create(['user_id' => $user->id]);

        return $user;
    }

    public function test_cv_show_renders_with_user_data(): void
    {
        $user = $this->seedFullCv();

        $response = $this->get(route('cv.show'));

        $response->assertStatus(200);
        $response->assertSee('CV User');
        $response->assertSee('CV Title');
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
