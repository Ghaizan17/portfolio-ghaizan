<?php

namespace Database\Factories;

use App\Models\GuestbookEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GuestbookEntry>
 */
class GuestbookEntryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'message' => fake()->paragraph(),
            'status' => GuestbookEntry::STATUS_PENDING,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (): array => ['status' => GuestbookEntry::STATUS_APPROVED]);
    }
}
