<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCvTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_cv_index(): void
    {
        $admin = User::factory()->admin()->create();
        Profile::factory()->create(['user_id' => $admin->id]);

        $response = $this->actingAs($admin)->get(route('admin.cv.index'));

        $response->assertStatus(200);
        $response->assertViewHas('user');
    }

    public function test_admin_can_update_profile(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->put(route('admin.cv.profile.update'), [
            'title' => 'Senior Engineer',
            'bio' => 'Bio actualizada',
            'location' => 'Madrid',
            'github_url' => 'https://github.com/test',
            'linkedin_url' => 'https://linkedin.com/in/test',
            'avatar_initials' => 'SE',
        ]);

        $response->assertRedirect(route('admin.cv.index'));
        $this->assertDatabaseHas('profiles', [
            'user_id' => $admin->id,
            'title' => 'Senior Engineer',
            'avatar_initials' => 'SE',
        ]);
    }

    public function test_profile_update_requires_title(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->put(route('admin.cv.profile.update'), [
            'title' => '',
        ]);

        $response->assertSessionHasErrors(['title']);
    }

    public function test_admin_can_create_experience(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.cv.experience.store'), [
            'role' => 'Backend Dev',
            'company' => 'Acme Inc',
            'period' => '2020 - 2022',
            'description' => 'Desarrollo backend',
            'sort_order' => 0,
        ]);

        $response->assertRedirect(route('admin.cv.index'));
        $this->assertDatabaseHas('experiences', [
            'user_id' => $admin->id,
            'role' => 'Backend Dev',
            'company' => 'Acme Inc',
        ]);
    }

    public function test_experience_store_requires_role_company_period(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.cv.experience.store'), []);

        $response->assertSessionHasErrors(['role', 'company', 'period']);
    }

    public function test_admin_can_delete_own_experience(): void
    {
        $admin = User::factory()->admin()->create();
        $experience = Experience::factory()->create(['user_id' => $admin->id]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.cv.experience.destroy', $experience));

        $response->assertRedirect(route('admin.cv.index'));
        $this->assertDatabaseMissing('experiences', ['id' => $experience->id]);
    }

    public function test_admin_cannot_delete_another_users_experience(): void
    {
        $admin1 = User::factory()->admin()->create();
        $admin2 = User::factory()->admin()->create();
        $experience = Experience::factory()->create(['user_id' => $admin1->id]);

        $response = $this->actingAs($admin2)
            ->delete(route('admin.cv.experience.destroy', $experience));

        $response->assertStatus(403);
        $this->assertDatabaseHas('experiences', ['id' => $experience->id]);
    }

    public function test_guest_cannot_access_admin_cv(): void
    {
        $response = $this->get(route('admin.cv.index'));

        $response->assertRedirect('/login');
    }
}
