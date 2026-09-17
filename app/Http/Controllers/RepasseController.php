<?php

namespace App\Http\Controllers;

use App\Enums\StatusRepasse;
use App\Models\Repasse;
use App\Services\CalculadoraRepasse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RepasseController extends Controller
{
    public function __construct(private CalculadoraRepasse $calculadoraRepasse)
    {
    }

    /** Atualiza retenções/custos e o status do repasse (ex.: marcar como Efetuado). */
    public function update(Request $request, Repasse $repasse): RedirectResponse
    {
        $dados = $request->validate([
            'valor_retido' => ['nullable', 'numeric', 'min:0'],
            'descricao_retido' => ['nullable', 'string', 'max:255'],
            'valor_custos' => ['nullable', 'numeric', 'min:0'],
            'descricao_custos' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::enum(StatusRepasse::class)],
            'data_repasse' => ['nullable', 'date'],
        ]);

        $valorRetido = (float) ($dados['valor_retido'] ?? 0);
        $valorCustos = (float) ($dados['valor_custos'] ?? 0);

        // Recalcula taxa_adm e total do repasse pela regra centralizada:
        // total_recebido - retido - taxa_adm (custos não entram aqui).
        $calculo = $this->calculadoraRepasse->calcular($repasse->fatura, $valorRetido, $valorCustos);

        $dados['valor_taxa_adm'] = $calculo['valor_taxa_adm'];
        $dados['valor_total_repasse'] = $calculo['valor_total_repasse'];

        if ($dados['status'] === StatusRepasse::EFETUADO->value && empty($dados['data_repasse'])) {
            $dados['data_repasse'] = now()->format('Y-m-d');
        }

        $repasse->update($dados);

        return redirect()
            ->route('faturas.show', $repasse->fatura)
            ->with('sucesso', 'Repasse atualizado com sucesso.');
    }
}
