<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCvExperienceUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_experience(): void
    {
        $admin = User::factory()->admin()->create();
        $exp = Experience::factory()->create(['user_id' => $admin->id, 'role' => 'Dev', 'company' => 'Acme', 'period' => '2020-2022']);

        $response = $this->actingAs($admin)->put(route('admin.cv.experience.update', $exp), [
            'role' => 'Senior Dev',
            'company' => 'Acme Corp',
            'period' => '2020-2023',
            'location' => 'Madrid',
            'competencies' => 'Laravel, PHP',
        ]);

        $response->assertRedirect(route('admin.cv.index'));
        $this->assertDatabaseHas('experiences', ['id' => $exp->id, 'role' => 'Senior Dev', 'location' => 'Madrid']);
        $this->assertDatabaseHas('experience_competencies', ['experience_id' => $exp->id, 'name' => 'Laravel']);
    }

    public function test_admin_cannot_update_another_users_experience(): void
    {
        $admin1 = User::factory()->admin()->create();
        $admin2 = User::factory()->admin()->create();
        $exp = Experience::factory()->create(['user_id' => $admin1->id, 'role' => 'Dev', 'company' => 'X', 'period' => '2020']);

        $response = $this->actingAs($admin2)->put(route('admin.cv.experience.update', $exp), [
            'role' => 'Hacked',
            'company' => 'X',
            'period' => '2020',
        ]);

        $response->assertStatus(403);
    }

    public function test_store_experience_syncs_competencies(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.cv.experience.store'), [
            'role' => 'Dev',
            'company' => 'Corp',
            'period' => '2022',
            'competencies' => 'Vue, Tailwind, MySQL',
        ]);

        $exp = $admin->experiences()->first();
        $this->assertNotNull($exp);
        $this->assertDatabaseHas('experience_competencies', ['experience_id' => $exp->id, 'name' => 'Vue']);
        $this->assertDatabaseHas('experience_competencies', ['experience_id' => $exp->id, 'name' => 'Tailwind']);
    }
}
