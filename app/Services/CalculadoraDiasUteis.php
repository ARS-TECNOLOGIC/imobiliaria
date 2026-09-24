<?php

namespace App\Services;

use App\Models\Feriado;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

/**
 * Sabe se uma data é dia útil (não é fim de semana nem feriado cadastrado
 * na tabela `feriados`) e sabe "empurrar" uma data para o próximo dia útil.
 *
 * Usado para aplicar a regra: se o vencimento cai em fim de semana ou
 * feriado, o pagamento feito no próximo dia útil não é considerado atrasado.
 */
class CalculadoraDiasUteis
{
    /** @var array<string, true>|null datas de feriado no formato Y-m-d, para consulta O(1) */
    private ?array $feriados = null;

    private function feriados(): array
    {
        if ($this->feriados === null) {
            $this->feriados = Schema::hasTable('feriados')
                ? Feriado::query()
                    ->pluck('data')
                    ->mapWithKeys(fn (Carbon $data) => [$data->format('Y-m-d') => true])
                    ->all()
                : [];
        }

        return $this->feriados;
    }

    public function isDiaUtil(Carbon $data): bool
    {
        return ! $data->isWeekend() && ! isset($this->feriados()[$data->format('Y-m-d')]);
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

    public function ultimoDiaUtil(Carbon $data): Carbon
    {
        $data = $data->copy();

        while (! $this->isDiaUtil($data)) {
            $data->subDay();
        }

        return $data;
    }
}
