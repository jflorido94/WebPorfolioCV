<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCvCourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_course(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.cv.course.store'), [
            'type' => 'course',
            'title' => 'React desde cero',
            'institution' => 'Udemy',
            'year' => 2024,
            'url' => 'https://udemy.com/certificate/abc123',
        ]);

        $response->assertRedirect(route('admin.cv.index'));
        $this->assertDatabaseHas('courses', [
            'type' => 'course',
            'title' => 'React desde cero',
            'user_id' => $admin->id,
        ]);
    }

    public function test_admin_can_store_certification(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.cv.course.store'), [
            'type' => 'certification',
            'title' => 'AWS Certified Developer',
            'institution' => 'Amazon Web Services',
            'year' => 2024,
        ]);

        $response->assertRedirect(route('admin.cv.index'));
        $this->assertDatabaseHas('courses', [
            'type' => 'certification',
            'title' => 'AWS Certified Developer',
        ]);
    }

    public function test_admin_can_update_course(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create(['user_id' => $admin->id, 'type' => 'course', 'title' => 'Old', 'institution' => 'Udemy']);

        $response = $this->actingAs($admin)->put(route('admin.cv.course.update', $course), [
            'type' => 'course',
            'title' => 'Updated Title',
            'institution' => 'Udemy',
        ]);

        $response->assertRedirect(route('admin.cv.index'));
        $this->assertDatabaseHas('courses', ['id' => $course->id, 'title' => 'Updated Title']);
    }

    public function test_admin_can_destroy_course(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create(['user_id' => $admin->id, 'type' => 'course', 'title' => 'X', 'institution' => 'Y']);

        $response = $this->actingAs($admin)->delete(route('admin.cv.course.destroy', $course));

        $response->assertRedirect(route('admin.cv.index'));
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_admin_cannot_destroy_another_users_course(): void
    {
        $admin1 = User::factory()->admin()->create();
        $admin2 = User::factory()->admin()->create();
        $course = Course::factory()->create(['user_id' => $admin1->id, 'type' => 'course', 'title' => 'X', 'institution' => 'Y']);

        $response = $this->actingAs($admin2)->delete(route('admin.cv.course.destroy', $course));

        $response->assertStatus(403);
        $this->assertDatabaseHas('courses', ['id' => $course->id]);
    }

    public function test_invalid_type_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.cv.course.store'), [
            'type' => 'invalid',
            'title' => 'Test',
            'institution' => 'Test',
        ]);

        $response->assertSessionHasErrors(['type']);
    }
}
