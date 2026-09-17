<?php

namespace Database\Factories;

use App\Enums\ServicoCondominio;
use App\Enums\TipoCobranca;
use App\Models\Imovel;
use App\Models\ImovelServicoCondominio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ImovelServicoCondominio>
 */
class ImovelServicoCondominioFactory extends Factory
{
    protected $model = ImovelServicoCondominio::class;

    public function definition(): array
    {
        $servico = fake()->randomElement(ServicoCondominio::cases());

        return [
            'imovel_id' => Imovel::factory(),
            'servico' => $servico->value,
            'descricao_outro' => $servico === ServicoCondominio::OUTRO ? fake()->words(3, true) : null,
            'tipo_cobranca' => fake()->randomElement(TipoCobranca::cases())->value,
            'observacao' => fake()->optional(0.3)->sentence(),
        ];
    }
}
