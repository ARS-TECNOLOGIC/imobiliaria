<?php

namespace Database\Factories;

use App\Models\Corretor;
use App\Models\Pessoa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Corretor>
 */
class CorretorFactory extends Factory
{
    protected $model = Corretor::class;

    public function definition(): array
    {
        return [
            'pessoa_id' => Pessoa::factory(),
            'user_id' => null,
            'creci' => fake()->unique()->numerify('CRECI-#####-F'),
            'percentual_comissao' => fake()->randomElement([20.00, 25.00, 30.00, 50.00]),
            'ativo' => true,
        ];
    }
}
