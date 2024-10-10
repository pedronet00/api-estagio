<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Missoes>
 */
class MissoesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomeMissao' => fake()->words(3, true), // Gera um título com 3 palavras
            'quantidadeMembros'=> fake()->numberBetween(5, 30), // Gera um parágrafo de texto
            'cidadeMissao' => fake()->words(2, true), // Gera uma URL de imagem fictícia
            'pastorTitular' => fake()->numberBetween(1, 100),
            'statusMissao' => fake()->numberBetween(0, 1),
            'idCliente' => fake()->numberBetween(1,3)
        ];
    }
}
