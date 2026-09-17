<?php

namespace Database\Factories;

use App\Enums\PapelContrato;
use App\Models\Contrato;
use App\Models\ContratoParte;
use App\Models\Pessoa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContratoParte>
 */
class ContratoParteFactory extends Factory
{
    protected $model = ContratoParte::class;

    public function definition(): array
    {
        return [
            'contrato_id' => Contrato::factory(),
            'pessoa_id' => Pessoa::factory(),
            'papel' => fake()->randomElement(PapelContrato::cases())->value,
            'assina_contrato' => true,
        ];
    }
}
