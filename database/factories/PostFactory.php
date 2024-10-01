<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tituloPost' => fake()->text(50),
            'subtituloPost' => fake()->text(100),
            'autorPost' => fake()->numberBetween(1, 3),
            'dataPost' => fake()->date(),
            'textoPost' => fake()->text(4000),
            'imgPost' => 'https://picsum.photos/seed/' . fake()->uuid() . '/1920/1080', // URL única com UUID
            'tipoPost' => fake()->numberBetween(1, 5),
            'statusPost' => fake()->boolean()
        ];
    }
}
