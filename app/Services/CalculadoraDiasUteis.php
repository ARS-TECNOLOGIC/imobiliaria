<?php

namespace App\Services;

use App\Models\Feriado;
use Carbon\Carbon;

/**
 * Sabe se uma data é dia útil (não é fim de semana nem feriado cadastrado
 * na tabela `feriados`) e sabe "empurrar" uma data para o próximo dia útil.
 *
 * Usado para aplicar a regra: se o vencimento cai em fim de semana ou
 * feriado, o pagamento feito no próximo dia útil não é considerado atrasado.
 */
class CalculadoraDiasUteis
{
    /** @var array<string, true> datas de feriado no formato Y-m-d, para consulta O(1) */
    private array $feriados;

    public function __construct()
    {
        $this->feriados = Feriado::query()
            ->pluck('data')
            ->mapWithKeys(fn (Carbon $data) => [$data->format('Y-m-d') => true])
            ->all();
    }

    public function isDiaUtil(Carbon $data): bool
    {
        return ! $data->isWeekend() && ! isset($this->feriados[$data->format('Y-m-d')]);
    }

    /** Se a data já for dia útil, retorna ela mesma. Senão, avança até o próximo. */
    public function proximoDiaUtil(Carbon $data): Carbon
    {
        $data = $data->copy();

        while (! $this->isDiaUtil($data)) {
            $data->addDay();
        }

        return $data;
    }
}
