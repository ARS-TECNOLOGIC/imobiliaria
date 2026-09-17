<?php

namespace Database\Factories;

use App\Enums\StatusSeguro;
use App\Enums\TipoSeguro;
use App\Models\Contrato;
use App\Models\ContratoSeguroFianca;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContratoSeguroFianca>
 */
class ContratoSeguroFiancaFactory extends Factory
{
    protected $model = ContratoSeguroFianca::class;

    public function definition(): array
    {
        $dataInicio = fake()->dateTimeBetween('-1 year', 'now');
        $dataVencimento = (clone $dataInicio)->modify('+1 year');

        return [
            'contrato_id' => Contrato::factory(),
            'tipo' => fake()->randomElement(TipoSeguro::cases())->value,
            'seguradora' => fake()->randomElement(['Porto Seguro', 'SulAmérica', 'Tokio Marine', 'Bradesco Seguros', 'Icatu']),
            'numero_apolice' => fake()->numerify('AP-########'),
            'valor_cobertura' => fake()->randomFloat(2, 10000, 100000),
            'valor_premio' => fake()->randomFloat(2, 300, 2500),
            'data_inicio' => $dataInicio->format('Y-m-d'),
            'data_vencimento_apolice' => $dataVencimento->format('Y-m-d'),
            'status' => StatusSeguro::ATIVO->value,
        ];
    }
}
