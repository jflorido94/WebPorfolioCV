<?php

namespace Tests\Feature;

use App\Models\Education;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCvEducationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_education(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.cv.education.store'), [
            'title' => 'Grado en Informatica',
            'institution' => 'Universidad de Malaga',
            'location' => 'Malaga',
            'year' => 2016,
        ]);

        $response->assertRedirect(route('admin.cv.index'));
        $this->assertDatabaseHas('education', [
            'title' => 'Grado en Informatica',
            'location' => 'Malaga',
            'user_id' => $admin->id,
        ]);
    }

    public function test_admin_can_update_education(): void
    {
        $admin = User::factory()->admin()->create();
        $edu = Education::factory()->create(['user_id' => $admin->id, 'title' => 'Old', 'institution' => 'UMA']);

        $response = $this->actingAs($admin)->put(route('admin.cv.education.update', $edu), [
            'title' => 'Nuevo Titulo',
            'institution' => 'UMA',
            'location' => 'Malaga',
        ]);

        $response->assertRedirect(route('admin.cv.index'));
        $this->assertDatabaseHas('education', ['id' => $edu->id, 'title' => 'Nuevo Titulo']);
    }

    public function test_admin_can_destroy_education(): void
    {
        $admin = User::factory()->admin()->create();
        $edu = Education::factory()->create(['user_id' => $admin->id, 'title' => 'A', 'institution' => 'B']);

        $response = $this->actingAs($admin)->delete(route('admin.cv.education.destroy', $edu));

        $response->assertRedirect(route('admin.cv.index'));
        $this->assertDatabaseMissing('education', ['id' => $edu->id]);
    }

    public function test_admin_cannot_destroy_another_users_education(): void
    {
        $admin1 = User::factory()->admin()->create();
        $admin2 = User::factory()->admin()->create();
        $edu = Education::factory()->create(['user_id' => $admin1->id, 'title' => 'A', 'institution' => 'B']);

        $response = $this->actingAs($admin2)->delete(route('admin.cv.education.destroy', $edu));

        $response->assertStatus(403);
        $this->assertDatabaseHas('education', ['id' => $edu->id]);
    }

    public function test_store_education_requires_title_and_institution(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.cv.education.store'), [
            'title' => '',
            'institution' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'institution']);
    }
}
