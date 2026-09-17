<?php

namespace Database\Factories;

use App\Enums\TipoVinculo;
use App\Models\Pessoa;
use App\Models\PessoaRelacionamento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PessoaRelacionamento>
 */
class PessoaRelacionamentoFactory extends Factory
{
    protected $model = PessoaRelacionamento::class;

    public function definition(): array
    {
        return [
            'pessoa_id' => Pessoa::factory(),
            'conjuge_id' => Pessoa::factory(),
            'tipo_vinculo' => fake()->randomElement(TipoVinculo::cases())->value,
        ];
    }
}
