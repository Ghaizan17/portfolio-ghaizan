<?php

namespace Database\Factories;

use App\Models\Certificate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'issuer' => fake()->company(),
            'issued_at' => fake()->date(),
            'category' => 'Workshop',
            'description' => fake()->paragraph(),
            'is_published' => true,
            'sort_order' => 0,
        ];
    }
}
