<?php

namespace Database\Factories;

use App\Enums\TipoImovel;
use App\Models\Imovel;
use App\Models\Pessoa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Imovel>
 */
class ImovelFactory extends Factory
{
    protected $model = Imovel::class;

    public function definition(): array
    {
        $possuiCondominio = fake()->boolean(40);

        return [
            'codigo' => 'IM-' . fake()->unique()->numerify('####'),
            'locador_id' => Pessoa::factory(),
            'tipo' => fake()->randomElement(TipoImovel::cases())->value,
            'cep' => fake()->numerify('#####-###'),
            'logradouro' => fake()->streetName(),
            'numero' => (string) fake()->numberBetween(1, 2000),
            'complemento' => fake()->optional(0.3)->secondaryAddress(),
            'bairro' => fake()->citySuffix() . ' ' . fake()->lastName(),
            'cidade' => 'Bauru',
            'uf' => 'SP',
            'possui_condominio' => $possuiCondominio,
            'nome_condominio' => $possuiCondominio ? 'Residencial ' . fake()->lastName() : null,
            'administradora_condominio' => $possuiCondominio ? fake()->company() : null,
        ];
    }
}
