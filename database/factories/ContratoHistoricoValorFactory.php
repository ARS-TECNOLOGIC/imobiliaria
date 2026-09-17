<?php

namespace Database\Factories;

use App\Enums\TipoCampoHistorico;
use App\Models\Contrato;
use App\Models\ContratoHistoricoValor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContratoHistoricoValor>
 */
class ContratoHistoricoValorFactory extends Factory
{
    protected $model = ContratoHistoricoValor::class;

    public function definition(): array
    {
        $valorAnterior = fake()->randomFloat(2, 800, 8000);

        return [
            'contrato_id' => Contrato::factory(),
            'tipo_campo' => fake()->randomElement(TipoCampoHistorico::cases())->value,
            'valor_anterior' => $valorAnterior,
            'valor_novo' => round($valorAnterior * fake()->randomFloat(2, 1.03, 1.12), 2),
            'motivo' => fake()->randomElement(['Reajuste anual (IGP-M)', 'Reajuste anual (IPCA)', 'Renegociação de contrato']),
            'alterado_por_user_id' => null,
        ];
    }
}
