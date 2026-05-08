<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AvatarUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_avatar(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();

        $file = UploadedFile::fake()->image('photo.jpg', 200, 200);

        $response = $this->actingAs($admin)->put(route('admin.cv.profile.update'), [
            'title' => 'Desarrollador Web',
            'avatar' => $file,
        ]);

        $response->assertRedirect(route('admin.cv.index'));

        $profile = $admin->fresh()->profile;
        $this->assertNotNull($profile?->avatar_path, 'avatar_path debe quedar guardado en DB');

        Storage::disk('public')->assertExists($profile->avatar_path);
    }

    public function test_admin_can_remove_avatar(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();

        // Crear el fichero previamente en el disco falso
        $existingPath = 'avatars/existing.jpg';
        Storage::disk('public')->put($existingPath, 'fake-image-content');

        Profile::factory()->create([
            'user_id' => $admin->id,
            'avatar_path' => $existingPath,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.cv.profile.update'), [
            'title' => 'Desarrollador Web',
            'remove_avatar' => '1',
        ]);

        $response->assertRedirect(route('admin.cv.index'));

        $this->assertNull($admin->fresh()->profile?->avatar_path, 'avatar_path debe ser null tras eliminar');
        Storage::disk('public')->assertMissing($existingPath);
    }

    public function test_avatar_must_be_image(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();

        $nonImage = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($admin)->put(route('admin.cv.profile.update'), [
            'title' => 'Desarrollador Web',
            'avatar' => $nonImage,
        ]);

        $response->assertSessionHasErrors(['avatar']);
    }
}
