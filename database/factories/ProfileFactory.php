<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'headline' => 'Creative Full-Stack Developer',
            'class' => 'Student',
            'vocational_competency' => 'Software Engineering',
            'bio_short' => fake()->sentence(20),
            'bio_long' => fake()->paragraphs(3, true),
            'personal_branding' => fake()->sentence(12),
            'career_goals' => fake()->sentence(16),
            'location' => fake()->city(),
        ];
    }
}
