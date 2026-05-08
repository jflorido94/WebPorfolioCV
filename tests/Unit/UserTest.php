<?php

namespace Tests\Unit;

use App\Models\Experience;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_admin_returns_true_for_admin_user(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertTrue($admin->isAdmin());
    }

    public function test_is_admin_returns_false_for_normal_user(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->isAdmin());
    }

    public function test_with_cv_scope_eager_loads_relations(): void
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        Experience::factory()->count(2)->create(['user_id' => $user->id]);

        $loaded = User::query()->withCv()->whereKey($user->id)->first();

        $this->assertTrue($loaded->relationLoaded('profile'));
        $this->assertTrue($loaded->relationLoaded('experiences'));
        $this->assertTrue($loaded->relationLoaded('education'));
        $this->assertTrue($loaded->relationLoaded('skills'));
    }

    public function test_years_of_experience_returns_zero_when_no_experiences(): void
    {
        $user = User::factory()->create();
        $user->load('experiences');

        $this->assertSame(0, $user->years_of_experience);
    }

    public function test_years_of_experience_calculates_from_earliest_started_at(): void
    {
        $user = User::factory()->create();
        Experience::factory()->create([
            'user_id' => $user->id,
            'started_at' => now()->subYears(7)->startOfYear(),
            'sort_order' => 1,
        ]);
        Experience::factory()->create([
            'user_id' => $user->id,
            'started_at' => now()->subYears(2)->startOfYear(),
            'sort_order' => 0,
        ]);

        $user->load('experiences');

        $this->assertSame(7, $user->years_of_experience);
    }
}
