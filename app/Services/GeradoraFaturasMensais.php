<?php

namespace App\Services;

use App\Enums\StatusPagamento;
use App\Enums\StatusRepasse;
use App\Models\Contrato;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GeradoraFaturasMensais
{
    public function __construct(private CalculadoraRepasse $calculadoraRepasse) {}

    public function gerar(Carbon $referencia): int
    {
        $referencia = $referencia->copy()->startOfMonth();
        $faturasGeradas = 0;

        Contrato::query()
            ->with('imovel')
            ->where('status', 'ATIVO')
            ->orderBy('id')
            ->each(function (Contrato $contrato) use ($referencia, &$faturasGeradas): void {
                $vencimento = $referencia->copy()->addMonth()->day($contrato->dia_vencimento);

                if ($contrato->data_inicio->isAfter($vencimento)) {
                    return;
                }

                $fatura = DB::transaction(function () use ($contrato, $referencia, $vencimento) {
                    $fatura = $contrato->faturas()->firstOrCreate(
                        ['referencia' => $referencia->toDateString()],
                        [
                            'data_vencimento' => $vencimento->toDateString(),
                            'valor_aluguel' => $contrato->valor_aluguel,
                            'valor_condominio' => $contrato->imovel->possui_condominio
                                ? $contrato->valor_condominio
                                : 0,
                            'valor_iptu' => $contrato->valor_iptu,
                            'parcela_iptu' => $contrato->parcela_iptu,
                            'valor_seguro' => $contrato->valor_seguro,
                            'valor_taxa_extra' => 0,
                            'valor_desconto' => 0,
                            'valor_multa_juros' => 0,
                            'valor_pago' => null,
                            'dias_atraso' => 0,
                            'isento_multa' => false,
                            'status_pagamento' => StatusPagamento::PENDENTE->value,
                        ],
                    );

                    if ($fatura->wasRecentlyCreated) {
                        $calculo = $this->calculadoraRepasse->calcular($fatura);

                        $fatura->repasse()->create([
                            'valor_taxa_adm' => $calculo['valor_taxa_adm'],
                            'valor_retido' => 0,
                            'valor_custos' => 0,
                            'valor_total_repasse' => $calculo['valor_total_repasse'],
                            'data_limite_repasse' => $vencimento->copy()->addDays(5),
                            'status' => StatusRepasse::PENDENTE->value,
                        ]);
                    }

                    return $fatura;
                });

                if ($fatura->wasRecentlyCreated) {
                    $faturasGeradas++;
                }
            });

        return $faturasGeradas;
    }
}
