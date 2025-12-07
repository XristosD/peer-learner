<?php

namespace Database\Factories;

use App\Enums\BookVisibility;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'user_id' => User::factory(),
        ];
    }

    /**
     * Indicate that the book is public.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => BookVisibility::Public,
        ]);
    }

    /**
     * Indicate that the book is private.
     */
    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => BookVisibility::Private,
        ]);
    }

    /**
     * Indicate that the book is default.
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }
}
