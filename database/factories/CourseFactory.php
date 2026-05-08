<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['course', 'certification']),
            'title' => fake()->sentence(3),
            'institution' => fake()->company(),
            'location' => null,
            'year' => fake()->year(),
            'url' => null,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
