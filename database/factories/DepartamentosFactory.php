<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Departamentos>
 */
class DepartamentosFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tituloDepartamento' => fake()->words(3, true), // Gera um título com 3 palavras
            'textoDepartamento' => fake()->paragraph, // Gera um parágrafo de texto
            'imgDepartamento' => fake()->imageUrl(640, 480, 'business', true, 'Departamento'), // Gera uma URL de imagem fictícia
            'statusDepartamento' => fake()->numberBetween(0, 1),
            'idCliente' => fake()->numberBetween(1,5)
        ];
    }
}
