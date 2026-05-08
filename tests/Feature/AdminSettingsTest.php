<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_settings_page(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.settings.index'));

        $response->assertOk();
    }

    public function test_admin_can_update_email(): void
    {
        $admin = User::factory()->admin()->create(['email' => 'old@example.com']);

        $response = $this->actingAs($admin)->put(route('admin.settings.email.update'), [
            'email' => 'new@example.com',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'email' => 'new@example.com']);
    }

    public function test_email_must_be_unique(): void
    {
        $admin1 = User::factory()->admin()->create(['email' => 'taken@example.com']);
        $admin2 = User::factory()->admin()->create(['email' => 'mine@example.com']);

        $response = $this->actingAs($admin2)->put(route('admin.settings.email.update'), [
            'email' => 'taken@example.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_admin_can_update_password(): void
    {
        $admin = User::factory()->admin()->create(['password' => Hash::make('oldpassword')]);

        $response = $this->actingAs($admin)->put(route('admin.settings.password.update'), [
            'current_password' => 'oldpassword',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $this->assertTrue(Hash::check('newpassword', $admin->fresh()->password));
    }

    public function test_wrong_current_password_fails(): void
    {
        $admin = User::factory()->admin()->create(['password' => Hash::make('correct')]);

        $response = $this->actingAs($admin)->put(route('admin.settings.password.update'), [
            'current_password' => 'wrong',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertSessionHasErrors(['current_password']);
    }

    public function test_password_must_be_confirmed(): void
    {
        $admin = User::factory()->admin()->create(['password' => Hash::make('current')]);

        $response = $this->actingAs($admin)->put(route('admin.settings.password.update'), [
            'current_password' => 'current',
            'password' => 'newpassword',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors(['password']);
    }
}
