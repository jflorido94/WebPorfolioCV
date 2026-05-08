<?php

namespace Database\Factories;

use App\Models\Education;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Education>
 */
class EducationFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->randomElement([
                'Grado en Ingenieria Informatica',
                'Master en Desarrollo Web',
                'Bootcamp Fullstack',
                'Curso de Especializacion',
            ]),
            'institution' => fake()->company(),
            'year' => fake()->numberBetween(2010, 2025),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
