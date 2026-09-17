<?php

namespace Database\Factories;

use App\Enums\StatusPagamento;
use App\Models\Contrato;
use App\Models\Fatura;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fatura>
 */
class FaturaFactory extends Factory
{
    protected $model = Fatura::class;

    public function definition(): array
    {
        $referencia = fake()->dateTimeBetween('-6 months', 'now')->modify('first day of this month');
        $dataVencimento = (clone $referencia)->modify('+' . fake()->numberBetween(5, 10) . ' days');

        return [
            'contrato_id' => Contrato::factory(),
            'referencia' => $referencia->format('Y-m-d'),
            'data_vencimento' => $dataVencimento->format('Y-m-d'),
            'valor_aluguel' => fake()->randomFloat(2, 800, 8000),
            'valor_condominio' => fake()->randomFloat(2, 0, 1200),
            'valor_iptu' => fake()->randomFloat(2, 0, 500),
            'parcela_iptu' => fake()->optional(0.4)->numerify('##/10'),
            'valor_seguro' => fake()->randomFloat(2, 0, 200),
            'valor_taxa_extra' => 0,
            'descricao_taxa_extra' => null,
            'valor_desconto' => 0,
            'descricao_desconto' => null,
            'valor_multa_juros' => 0,
            'valor_pago' => null,
            'dias_atraso' => 0,
            'status_pagamento' => StatusPagamento::PENDENTE->value,
            'data_recebimento' => null,
            'banco_recebedor' => null,
            'descricao_boleto' => null,
        ];
    }

    public function paga(): static
    {
        return $this->state(function (array $attributes) {
            $total = $attributes['valor_aluguel'] + $attributes['valor_condominio']
                + $attributes['valor_iptu'] + $attributes['valor_seguro'];

            return [
                'status_pagamento' => StatusPagamento::PAGO->value,
                'valor_pago' => round($total, 2),
                'data_recebimento' => $attributes['data_vencimento'],
                'banco_recebedor' => fake()->randomElement(['Itaú', 'Bradesco', 'Banco do Brasil']),
            ];
        });
    }

    public function pagaComAtraso(): static
    {
        return $this->state(function (array $attributes) {
            $total = $attributes['valor_aluguel'] + $attributes['valor_condominio']
                + $attributes['valor_iptu'] + $attributes['valor_seguro'];
            $multa = round($total * 0.02, 2);

            return [
                'status_pagamento' => StatusPagamento::PAGO_COM_ATRASO->value,
                'valor_multa_juros' => $multa,
                'valor_pago' => round($total + $multa, 2),
                'dias_atraso' => fake()->numberBetween(1, 15),
                'data_recebimento' => date('Y-m-d', strtotime($attributes['data_vencimento'] . ' +' . fake()->numberBetween(1, 15) . ' days')),
                'banco_recebedor' => fake()->randomElement(['Itaú', 'Bradesco', 'Banco do Brasil']),
            ];
        });
    }

    public function atrasada(): static
    {
        return $this->state(fn () => [
            'status_pagamento' => StatusPagamento::ATRASADO->value,
            'dias_atraso' => fake()->numberBetween(1, 30),
        ]);
    }
}
