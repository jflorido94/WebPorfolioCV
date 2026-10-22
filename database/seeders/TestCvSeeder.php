<?php

namespace Database\Seeders;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * CV completo con datos ficticios para los tests.
 *
 * Usa solo factories (Faker): nunca datos personales reales. Los datos reales
 * del propietario viven únicamente en AdminSeeder.
 *
 * Uso en un test: $this->seed(TestCvSeeder::class);
 * Los valores fijos (USER_NAME, PROFILE_TITLE) permiten hacer aserciones.
 */
class TestCvSeeder extends Seeder
{
    public const USER_NAME = 'CV User';

    public const PROFILE_TITLE = 'CV Title';

    public function run(): void
    {
        $user = User::factory()->admin()->create(['name' => self::USER_NAME]);

        Profile::factory()->create(['user_id' => $user->id, 'title' => self::PROFILE_TITLE]);
        Experience::factory()->count(2)->create(['user_id' => $user->id]);
        Education::factory()->create(['user_id' => $user->id]);
        Skill::factory()->count(3)->create(['user_id' => $user->id]);
    }
}
