<?php

namespace Database\Factories;

use App\Models\Experience;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Experience>
 */
class ExperienceFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-10 years', '-2 years');
        $end = fake()->dateTimeBetween($start, 'now');

        return [
            'user_id' => User::factory(),
            'role' => fake()->jobTitle(),
            'company' => fake()->company(),
            'period' => $start->format('Y') . ' - ' . $end->format('Y'),
            'description' => fake()->paragraph(),
            'started_at' => $start,
            'ended_at' => $end,
        ];
    }
}
