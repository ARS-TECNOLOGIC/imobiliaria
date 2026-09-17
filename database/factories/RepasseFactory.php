<?php

namespace Database\Factories;

use App\Enums\StatusRepasse;
use App\Models\Fatura;
use App\Models\Repasse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Repasse>
 */
class RepasseFactory extends Factory
{
    protected $model = Repasse::class;

    public function definition(): array
    {
        $valorBase = fake()->randomFloat(2, 800, 8000);
        $taxaAdm = round($valorBase * 0.08, 2);

        return [
            'fatura_id' => Fatura::factory(),
            'valor_taxa_adm' => $taxaAdm,
            'valor_retido' => 0,
            'descricao_retido' => null,
            'valor_custos' => 0,
            'descricao_custos' => null,
            'valor_total_repasse' => round($valorBase - $taxaAdm, 2),
            'data_limite_repasse' => fake()->dateTimeBetween('now', '+10 days')->format('Y-m-d'),
            'data_repasse' => null,
            'status' => StatusRepasse::PENDENTE->value,
        ];
    }

    public function efetuado(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusRepasse::EFETUADO->value,
            'data_repasse' => $attributes['data_limite_repasse'],
        ]);
    }
}
