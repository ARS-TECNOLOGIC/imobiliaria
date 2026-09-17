<?php

namespace Database\Factories;

use App\Enums\Garantia;
use App\Enums\StatusContrato;
use App\Models\Contrato;
use App\Models\Imovel;
use App\Models\Pessoa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contrato>
 */
class ContratoFactory extends Factory
{
    protected $model = Contrato::class;

    public function definition(): array
    {
        $dataInicio = fake()->dateTimeBetween('-2 years', '-1 month');

        return [
            'imovel_id' => Imovel::factory(),
            'favorecido_id' => Pessoa::factory(),
            'data_inicio' => $dataInicio->format('Y-m-d'),
            'data_fim' => null,
            'dia_vencimento' => fake()->numberBetween(1, 28),
            'garantia' => fake()->randomElement(Garantia::cases())->value,
            'valor_aluguel' => fake()->randomFloat(2, 800, 8000),
            'valor_condominio' => fake()->randomFloat(2, 0, 1200),
            'valor_iptu' => fake()->randomFloat(2, 0, 500),
            'parcela_iptu' => fake()->optional(0.5)->numerify('##/10'),
            'valor_seguro' => fake()->randomFloat(2, 0, 200),
            'taxa_adm_percentual' => fake()->randomElement([6.00, 8.00, 10.00]),
            'status' => StatusContrato::ATIVO->value,
        ];
    }

    public function encerrado(): static
    {
        return $this->state(fn () => [
            'status' => StatusContrato::ENCERRADO->value,
            'data_fim' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
        ]);
    }

    public function suspenso(): static
    {
        return $this->state(fn () => ['status' => StatusContrato::SUSPENSO->value]);
    }
}
