<?php

namespace Database\Factories;

use App\Models\Education;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Education>
 */
class EducationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'institution' => fake()->company().' School',
            'degree' => 'Vocational High School',
            'field_of_study' => 'Software Engineering',
            'level' => 'SMK',
            'start_year' => 2023,
            'end_year' => 2026,
            'is_current' => true,
            'sort_order' => 0,
        ];
    }
}
