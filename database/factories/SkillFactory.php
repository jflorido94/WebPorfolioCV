<?php

namespace Database\Factories;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement([
                'PHP', 'Laravel', 'MySQL', 'JavaScript', 'Vue.js',
                'React', 'Docker', 'Tailwind CSS', 'Git', 'PostgreSQL',
            ]),
            'category' => fake()->randomElement(['Backend', 'Frontend', 'DevOps', 'Database', 'general']),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
