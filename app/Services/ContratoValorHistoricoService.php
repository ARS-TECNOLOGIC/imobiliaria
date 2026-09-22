<?php

namespace App\Services;

use App\Enums\TipoCampoHistorico;
use App\Models\Contrato;
use App\Models\ContratoHistoricoValor;

class ContratoValorHistoricoService
{
    /**
     * Campos monetários monitorados e o tipo_campo correspondente no histórico.
     *
     * @var array<string, TipoCampoHistorico>
     */
    private const CAMPOS = [
        'valor_aluguel' => TipoCampoHistorico::ALUGUEL,
        'valor_condominio' => TipoCampoHistorico::CONDOMINIO,
        'valor_iptu' => TipoCampoHistorico::IPTU,
        'valor_seguro' => TipoCampoHistorico::SEGURO,
    ];

    /** @return array<string, TipoCampoHistorico> */
    public static function camposMonitorados(): array
    {
        return self::CAMPOS;
    }

    public function registrarValoresIniciais(Contrato $contrato, ?string $motivo = 'Valor inicial do contrato'): void
    {
        $userId = auth()->id();

        foreach (self::CAMPOS as $campo => $tipo) {
            ContratoHistoricoValor::create([
                'contrato_id' => $contrato->id,
                'tipo_campo' => $tipo,
                'valor_anterior' => 0,
                'valor_novo' => (float) $contrato->getAttribute($campo),
                'motivo' => $motivo,
                'alterado_por_user_id' => $userId,
            ]);
        }
    }

    /**
     * Grava uma linha de histórico para cada campo monetário cujo valor
     * difira do valor anterior capturado antes do fill().
     *
     * @param  array<string, float|int|string|null>  $valoresAnteriores
     */
    public function registrarAlteracoes(Contrato $contrato, array $valoresAnteriores, ?string $motivo = null): void
    {
        $userId = auth()->id();

        foreach (self::CAMPOS as $campo => $tipo) {
            $anterior = (float) ($valoresAnteriores[$campo] ?? 0);
            $novo = (float) $contrato->getAttribute($campo);

            if (abs($anterior - $novo) < 0.001) {
                continue;
            }

            ContratoHistoricoValor::create([
                'contrato_id' => $contrato->id,
                'tipo_campo' => $tipo,
                'valor_anterior' => $anterior,
                'valor_novo' => $novo,
                'motivo' => $motivo,
                'alterado_por_user_id' => $userId,
            ]);
        }
    }
}
