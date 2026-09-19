<?php

namespace Database\Seeders;

use App\Enums\CategoriaArmazenamento;
use App\Enums\Garantia;
use App\Enums\PapelContrato;
use App\Enums\StatusPagamento;
use App\Enums\StatusRepasse;
use App\Models\Contrato;
use App\Models\ContratoParte;
use App\Models\ContratoSeguroFianca;
use App\Models\Corretor;
use App\Models\Documento;
use App\Models\Fatura;
use App\Models\Imovel;
use App\Models\ImovelServicoCondominio;
use App\Models\Pessoa;
use App\Models\Repasse;
use Illuminate\Database\Seeder;

class SistemaLocacaoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Corretores com sua própria pessoa física
        $corretores = Corretor::factory()
            ->count(3)
            ->create();

        // 2. Locadores, locatários e fiadores
        $locadores = Pessoa::factory()->count(6)->create();
        $locatarios = Pessoa::factory()->count(10)->create();
        $fiadores = Pessoa::factory()->count(6)->create();

        // 3. Imóveis, um a três por locador
        $imoveis = collect();
        foreach ($locadores as $locador) {
            $imoveis = $imoveis->merge(
                Imovel::factory()
                    ->count(fake()->numberBetween(1, 3))
                    ->create(['locador_id' => $locador->id])
            );
        }

        // 4. Serviços de condomínio para os imóveis que possuem condomínio
        $imoveis->where('possui_condominio', true)->each(function (Imovel $imovel) {
            ImovelServicoCondominio::factory()
                ->count(fake()->numberBetween(1, 3))
                ->create(['imovel_id' => $imovel->id]);
        });

        // 5. Carteira: atribui cada imóvel a um corretor aleatório
        $imoveis->each(function (Imovel $imovel) use ($corretores) {
            $imovel->corretores()->attach($corretores->random()->id, [
                'data_atribuicao' => now()->subMonths(fake()->numberBetween(1, 12)),
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        // 6. Contratos: cria um contrato ativo para 80% dos imóveis
        $imoveisComContrato = $imoveis->random((int) ceil($imoveis->count() * 0.8));

        foreach ($imoveisComContrato as $imovel) {
            $locatario = $locatarios->random();
            $garantia = fake()->randomElement(Garantia::cases());

            $contrato = Contrato::factory()->create([
                'imovel_id' => $imovel->id,
                'favorecido_id' => $imovel->locador_id,
                'garantia' => $garantia->value,
            ]);

            // Partes do contrato
            ContratoParte::factory()->create([
                'contrato_id' => $contrato->id,
                'pessoa_id' => $imovel->locador_id,
                'papel' => PapelContrato::LOCADOR_TITULAR->value,
            ]);

            ContratoParte::factory()->create([
                'contrato_id' => $contrato->id,
                'pessoa_id' => $locatario->id,
                'papel' => PapelContrato::LOCATARIO_TITULAR->value,
            ]);

            // 40% dos contratos têm fiador (quando a garantia é FIADOR)
            if ($garantia === Garantia::FIADOR) {
                $fiador = $fiadores->random();

                ContratoParte::factory()->create([
                    'contrato_id' => $contrato->id,
                    'pessoa_id' => $fiador->id,
                    'papel' => PapelContrato::FIADOR_TITULAR->value,
                ]);
            }

            // Seguro/fiança vinculado quando a garantia exige apólice
            if (in_array($garantia, [Garantia::SEGURO_FIANCA, Garantia::TITULO_CAPITALIZACAO], true)) {
                ContratoSeguroFianca::factory()->create([
                    'contrato_id' => $contrato->id,
                    'tipo' => $garantia === Garantia::SEGURO_FIANCA ? 'FIANCA_LOCATICA' : 'TITULO_CAPITALIZACAO',
                ]);
            }

            // 7. Faturas dos últimos 4 meses, com status variado
            // Regra: referência = mês X, vencimento = mês X+1 (dia_vencimento do contrato)
            for ($mesesAtras = 3; $mesesAtras >= 0; $mesesAtras--) {
                $referencia = now()->copy()->subMonths($mesesAtras)->startOfMonth();
                $dataVencimento = $referencia->copy()->addMonth()->day(min($contrato->dia_vencimento, 28));

                $estado = match (true) {
                    // No mês corrente, só marca "atrasada" se o vencimento já passou de verdade.
                    $mesesAtras === 0 => $dataVencimento->isPast()
                        ? 'atrasada'
                        : 'pendente',
                    default => fake()->randomElement(['paga', 'paga', 'paga_com_atraso']),
                };

                $fatura = match ($estado) {
                    'paga' => Fatura::factory()->paga(),
                    'paga_com_atraso' => Fatura::factory()->pagaComAtraso(),
                    'atrasada' => Fatura::factory()->atrasada(),
                    default => Fatura::factory(),
                };

                $fatura = $fatura->create([
                    'contrato_id' => $contrato->id,
                    'referencia' => $referencia->format('Y-m-d'),
                    'data_vencimento' => $dataVencimento->format('Y-m-d'),
                    'valor_aluguel' => $contrato->valor_aluguel,
                    'valor_condominio' => $contrato->valor_condominio,
                    'valor_iptu' => $contrato->valor_iptu,
                    'valor_seguro' => $contrato->valor_seguro,
                ]);

                // 8. Repasse correspondente (pendente ou efetuado conforme a fatura)
                $repasseFactory = in_array($fatura->status_pagamento, [StatusPagamento::PAGO, StatusPagamento::PAGO_COM_ATRASO], true)
                    ? Repasse::factory()->efetuado()
                    : Repasse::factory();

                $repasseFactory->create([
                    'fatura_id' => $fatura->id,
                    'valor_taxa_adm' => round($contrato->valor_aluguel * ($contrato->taxa_adm_percentual / 100), 2),
                    'valor_total_repasse' => round($contrato->valor_aluguel - ($contrato->valor_aluguel * ($contrato->taxa_adm_percentual / 100)), 2),
                    'status' => $fatura->status_pagamento === StatusPagamento::PENDENTE || $fatura->status_pagamento === StatusPagamento::ATRASADO
                        ? StatusRepasse::PENDENTE->value
                        : StatusRepasse::EFETUADO->value,
                ]);
            }

            // 9. Documento de exemplo anexado ao contrato
            Documento::factory()->create([
                'documentavel_type' => Contrato::class,
                'documentavel_id' => $contrato->id,
                'tipo_documento' => 'CONTRATO_ASSINADO',
                'categoria_armazenamento' => CategoriaArmazenamento::DOCUMENTOS_ASSINADOS,
            ]);
        }
    }
}
