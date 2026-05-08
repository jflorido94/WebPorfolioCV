<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->jobTitle(),
            'bio' => fake()->paragraph(),
            'location' => fake()->city(),
            'github_url' => 'https://github.com/' . fake()->userName(),
            'linkedin_url' => 'https://linkedin.com/in/' . fake()->userName(),
            'avatar_initials' => strtoupper(fake()->lexify('??')),
        ];
    }
}
