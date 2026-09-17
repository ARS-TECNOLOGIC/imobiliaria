<?php

namespace App\Services;

use App\Models\Fatura;

/**
 * Regra de negócio do repasse ao locador:
 *
 *   valor_total_repasse = valor_total_recebido - valor_retido - valor_taxa_adm
 *
 * onde valor_taxa_adm incide sobre (aluguel + multa, quando existir) — a
 * administradora também fica com uma parte da multa cobrada, não só do
 * aluguel.
 *
 * "Custos" NÃO entram na conta do repasse (o locador recebe o mesmo,
 * independente dos custos da administradora). Custos servem só para saber
 * a receita líquida da própria administradora: taxa_adm - custos.
 */
class CalculadoraRepasse
{
    /** @return array{valor_taxa_adm: float, valor_total_repasse: float, valor_liquido_administradora: float} */
    public function calcular(Fatura $fatura, float $valorRetido = 0.0, float $valorCustos = 0.0): array
    {
        $contrato = $fatura->contrato;

        // Total recebido do locatário: se a fatura já foi paga, usa o valor
        // realmente pago; senão, usa o total previsto da fatura (estimativa,
        // já que ainda não se sabe se vai atrasar).
        $totalRecebido = $fatura->valor_pago !== null
            ? (float) $fatura->valor_pago
            : $this->totalPrevistoFatura($fatura);

        $taxaAdm = round(
            ((float) $fatura->valor_aluguel + (float) $fatura->valor_multa_juros)
                * ((float) $contrato->taxa_adm_percentual / 100),
            2
        );

        $totalRepasse = round($totalRecebido - $valorRetido - $taxaAdm, 2);
        $liquidoAdministradora = round($taxaAdm - $valorCustos, 2);

        return [
            'valor_taxa_adm' => $taxaAdm,
            'valor_total_repasse' => $totalRepasse,
            'valor_liquido_administradora' => $liquidoAdministradora,
        ];
    }

    private function totalPrevistoFatura(Fatura $fatura): float
    {
        return (float) $fatura->valor_aluguel
            + (float) $fatura->valor_condominio
            + (float) $fatura->valor_iptu
            + (float) $fatura->valor_seguro
            + (float) $fatura->valor_taxa_extra
            - (float) $fatura->valor_desconto
            + (float) $fatura->valor_multa_juros;
    }
}
