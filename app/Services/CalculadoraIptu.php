<?php

namespace App\Services;

class CalculadoraIptu
{
    public function mesTemIptu(int $mes): bool
    {
        $inicio = (int) configuracao('iptu_mes_inicio', 3);
        $qtd = (int) configuracao('iptu_qtd_parcelas', 10);
        $fim = min(12, $inicio + $qtd - 1);

        return $mes >= $inicio && $mes <= $fim;
    }

    public function getParcelasAno(int $ano): array
    {
        $inicio = (int) configuracao('iptu_mes_inicio', 3);
        $qtd = (int) configuracao('iptu_qtd_parcelas', 10);
        $fim = min(12, $inicio + $qtd - 1);

        return range($inicio, $fim);
    }

    public function deveAplicarGlobalmente(): bool
    {
        return (bool) configuracao('iptu_aplicar_globalmente', true);
    }
}
