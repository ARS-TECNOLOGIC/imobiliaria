<?php

namespace App\Console\Commands;

use App\Enums\StatusPagamento;
use App\Models\Fatura;
use App\Services\CalculadoraDiasUteis;
use Illuminate\Console\Command;

class AtualizarFaturasVencidas extends Command
{
    protected $signature = 'faturas:atualizar-vencidas';

    protected $description = 'Marca como ATRASADO as faturas PENDENTE cujo vencimento (ajustado para dia útil) já passou, calcula os dias de atraso e aplica a multa do contrato.';

    public function handle(CalculadoraDiasUteis $diasUteis): int
    {
        $hoje = today();
        $marcadasAtrasado = 0;
        $atualizadas = 0;

        Fatura::query()
            ->whereIn('status_pagamento', [StatusPagamento::PENDENTE->value, StatusPagamento::ATRASADO->value])
            ->with('contrato')
            ->each(function (Fatura $fatura) use ($hoje, $diasUteis, &$marcadasAtrasado, &$atualizadas) {
                // Se o vencimento cair em fim de semana/feriado, o prazo real
                // só vence no próximo dia útil — é esse vencimento efetivo
                // que decide se a fatura está atrasada ou não.
                $vencimentoEfetivo = $diasUteis->proximoDiaUtil($fatura->data_vencimento->copy());

                if ($hoje->lessThanOrEqualTo($vencimentoEfetivo)) {
                    return; // ainda dentro do prazo, nada a fazer
                }

                $diasAtraso = $vencimentoEfetivo->diffInDays($hoje);

                $base = $fatura->valor_aluguel + $fatura->valor_condominio
                    + $fatura->valor_iptu + $fatura->valor_seguro;
                $multa = round((float) $base * ((float) $fatura->contrato->multa_percentual / 100), 2);

                $eraAtrasado = $fatura->status_pagamento === StatusPagamento::ATRASADO;

                $fatura->update([
                    'status_pagamento' => StatusPagamento::ATRASADO->value,
                    'dias_atraso' => $diasAtraso,
                    'valor_multa_juros' => $multa,
                ]);

                $eraAtrasado ? $atualizadas++ : $marcadasAtrasado++;
            });

        $this->info(sprintf(
            '%d fatura(s) marcada(s) como atrasada(s) agora. %d fatura(s) já atrasada(s) tiveram dias de atraso e multa recalculados.',
            $marcadasAtrasado,
            $atualizadas,
        ));

        return self::SUCCESS;
    }
}
