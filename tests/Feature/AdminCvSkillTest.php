<?php

namespace Tests\Feature;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCvSkillTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_skill(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.cv.skill.store'), [
            'name' => 'Laravel',
            'category' => 'Backend',
        ]);

        $response->assertRedirect(route('admin.cv.index'));
        $this->assertDatabaseHas('skills', ['name' => 'Laravel', 'category' => 'Backend', 'user_id' => $admin->id]);
    }

    public function test_admin_can_update_skill(): void
    {
        $admin = User::factory()->admin()->create();
        $skill = Skill::factory()->create(['user_id' => $admin->id, 'name' => 'Vue', 'category' => 'Frontend']);

        $response = $this->actingAs($admin)->put(route('admin.cv.skill.update', $skill), [
            'name' => 'Vue 3',
            'category' => 'Frontend',
        ]);

        $response->assertRedirect(route('admin.cv.index'));
        $this->assertDatabaseHas('skills', ['id' => $skill->id, 'name' => 'Vue 3']);
    }

    public function test_admin_can_destroy_skill(): void
    {
        $admin = User::factory()->admin()->create();
        $skill = Skill::factory()->create(['user_id' => $admin->id, 'name' => 'PHP', 'category' => 'Backend']);

        $response = $this->actingAs($admin)->delete(route('admin.cv.skill.destroy', $skill));

        $response->assertRedirect(route('admin.cv.index'));
        $this->assertDatabaseMissing('skills', ['id' => $skill->id]);
    }

    public function test_admin_cannot_destroy_another_users_skill(): void
    {
        $admin1 = User::factory()->admin()->create();
        $admin2 = User::factory()->admin()->create();
        $skill = Skill::factory()->create(['user_id' => $admin1->id, 'name' => 'PHP', 'category' => 'Backend']);

        $response = $this->actingAs($admin2)->delete(route('admin.cv.skill.destroy', $skill));

        $response->assertStatus(403);
        $this->assertDatabaseHas('skills', ['id' => $skill->id]);
    }

    public function test_store_skill_requires_name_and_category(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.cv.skill.store'), [
            'name' => '',
            'category' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'category']);
    }
}
