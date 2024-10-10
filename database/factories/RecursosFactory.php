<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recursos>
 */
class RecursosFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomeRecurso' => fake()->words(3, true), // Gera um título com 3 palavras
            'tipoRecurso' => fake()->numberBetween(1, 10), // Gera um parágrafo de texto
            'categoriaRecurso' => fake()->numberBetween(1, 6), // Gera uma URL de imagem fictícia
            'quantidadeRecurso' => fake()->numberBetween(0, 100),
            'idCliente' => fake()->numberBetween(1,2)
        ];
    }
}
