<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCvVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_experience_hidden_in_web_not_shown_on_cv_page(): void
    {
        $user = User::factory()->admin()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        Experience::factory()->create([
            'user_id' => $user->id,
            'role' => 'Rol Secreto',
            'company' => 'Empresa Oculta',
            'show_in_web' => false,
            'show_in_pdf' => true,
        ]);

        $response = $this->get(route('cv.show'));

        $response->assertStatus(200);
        $response->assertDontSee('Rol Secreto');
    }

    public function test_skill_hidden_in_pdf_not_in_print(): void
    {
        $user = User::factory()->admin()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        Skill::factory()->create([
            'user_id' => $user->id,
            'name' => 'HabilidadOcultaEnPDF',
            'category' => 'Backend',
            'show_in_web' => true,
            'show_in_pdf' => false,
        ]);

        $response = $this->get(route('cv.print'));

        $response->assertStatus(200);
        $response->assertDontSee('HabilidadOcultaEnPDF');
    }

    public function test_visibility_checkboxes_saved_correctly_on_store(): void
    {
        $admin = User::factory()->admin()->create();

        // Enviar ambos checkboxes marcados
        $this->actingAs($admin)->post(route('admin.cv.experience.store'), [
            'role' => 'Desarrollador',
            'company' => 'Empresa',
            'period' => '2023 - Actualidad',
            'show_in_web' => '1',
            'show_in_pdf' => '1',
        ]);

        $this->assertDatabaseHas('experiences', [
            'user_id' => $admin->id,
            'role' => 'Desarrollador',
            'show_in_web' => true,
            'show_in_pdf' => true,
        ]);

        // Enviar sin checkboxes (equivale a desmarcarlos en el formulario HTML)
        $this->actingAs($admin)->post(route('admin.cv.experience.store'), [
            'role' => 'Desarrollador Oculto',
            'company' => 'Empresa',
            'period' => '2022 - 2023',
        ]);

        $this->assertDatabaseHas('experiences', [
            'user_id' => $admin->id,
            'role' => 'Desarrollador Oculto',
            'show_in_web' => false,
            'show_in_pdf' => false,
        ]);
    }
}
