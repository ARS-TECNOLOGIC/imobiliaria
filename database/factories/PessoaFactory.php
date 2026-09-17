<?php

namespace Database\Factories;

use App\Enums\EstadoCivil;
use App\Enums\RegimeBens;
use App\Enums\TipoConta;
use App\Models\Pessoa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pessoa>
 */
class PessoaFactory extends Factory
{
    protected $model = Pessoa::class;

    public function definition(): array
    {
        $estadoCivil = fake()->randomElement(EstadoCivil::cases());
        $temRegimeBens = in_array($estadoCivil, [EstadoCivil::CASADO, EstadoCivil::UNIAO_ESTAVEL], true);
        $tipoChavePix = fake()->randomElement(['CPF', 'EMAIL', 'TELEFONE', 'ALEATORIA']);

        return [
            'nome' => fake()->name(),
            'cpf_cnpj' => fake()->unique()->cpf(),
            'rg' => fake()->numerify('##.###.###-#'),
            'rg_orgao_emissor' => fake()->randomElement(['SSP-SP', 'SSP-RJ', 'SSP-MG', 'DETRAN-SP']),
            'data_nascimento' => fake()->dateTimeBetween('-75 years', '-18 years')->format('Y-m-d'),
            'estado_civil' => $estadoCivil->value,
            'regime_bens' => $temRegimeBens ? fake()->randomElement(RegimeBens::cases())->value : null,
            'email' => fake()->unique()->safeEmail(),
            'telefone' => fake()->numerify('(##) ####-####'),
            'celular' => fake()->numerify('(##) 9####-####'),
            'cep' => fake()->numerify('#####-###'),
            'logradouro' => fake()->streetName(),
            'numero' => (string) fake()->numberBetween(1, 2000),
            'complemento' => fake()->optional(0.4)->secondaryAddress(),
            'bairro' => fake()->citySuffix() . ' ' . fake()->lastName(),
            'cidade' => fake()->city(),
            'uf' => fake()->stateAbbr(),
            'tipo_chave_pix' => $tipoChavePix,
            'chave_pix' => match ($tipoChavePix) {
                'CPF' => fake()->cpf(),
                'EMAIL' => fake()->safeEmail(),
                'TELEFONE' => fake()->numerify('##9########'),
                default => fake()->uuid(),
            },
            'banco' => fake()->randomElement(['Itaú', 'Bradesco', 'Banco do Brasil', 'Caixa Econômica', 'Nubank', 'Santander']),
            'agencia' => fake()->numerify('####'),
            'conta' => fake()->numerify('#####-#'),
            'tipo_conta' => fake()->randomElement(TipoConta::cases())->value,
        ];
    }
}
