<?php

namespace Database\Factories;

use App\Enums\TipoDocumento;
use App\Models\Contrato;
use App\Models\Documento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Documento>
 */
class DocumentoFactory extends Factory
{
    protected $model = Documento::class;

    public function definition(): array
    {
        // Padrão aponta para um Contrato; use ->for($model, 'documentavel')
        // no seeder para anexar a Pessoa, Imovel ou Fatura.
        return [
            'documentavel_type' => Contrato::class,
            'documentavel_id' => Contrato::factory(),
            'tipo_documento' => fake()->randomElement(TipoDocumento::cases())->value,
            'nome_original' => fake()->word() . '.pdf',
            'caminho_arquivo' => 'documentos/' . fake()->uuid() . '.pdf',
            'mime_type' => 'application/pdf',
            'tamanho_bytes' => fake()->numberBetween(50_000, 5_000_000),
            'descricao' => fake()->optional(0.4)->sentence(),
            'enviado_por_user_id' => null,
        ];
    }
}
