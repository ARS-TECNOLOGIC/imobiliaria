<?php

namespace Database\Factories;

use App\Models\Corretor;
use App\Models\CorretorImovel;
use App\Models\Imovel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CorretorImovel>
 */
class CorretorImovelFactory extends Factory
{
    protected $model = CorretorImovel::class;

    public function definition(): array
    {
        return [
            'corretor_id' => Corretor::factory(),
            'imovel_id' => Imovel::factory(),
            'data_atribuicao' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'data_fim' => null,
            'ativo' => true,
        ];
    }
}
