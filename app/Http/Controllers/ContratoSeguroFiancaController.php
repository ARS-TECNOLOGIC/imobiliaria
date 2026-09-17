<?php

namespace App\Http\Controllers;

use App\Enums\StatusSeguro;
use App\Enums\TipoSeguro;
use App\Models\Contrato;
use App\Models\ContratoSeguroFianca;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContratoSeguroFiancaController extends Controller
{
    public function store(Request $request, Contrato $contrato): RedirectResponse
    {
        $dados = $request->validate([
            'tipo' => ['required', Rule::enum(TipoSeguro::class)],
            'seguradora' => ['required', 'string', 'max:100'],
            'numero_apolice' => ['nullable', 'string', 'max:100'],
            'valor_cobertura' => ['nullable', 'numeric', 'min:0'],
            'valor_premio' => ['nullable', 'numeric', 'min:0'],
            'data_inicio' => ['required', 'date'],
            'data_vencimento_apolice' => ['required', 'date', 'after:data_inicio'],
            'status' => ['required', Rule::enum(StatusSeguro::class)],
        ]);

        $contrato->segurosFiancas()->create($dados);

        return redirect()
            ->route('contratos.show', $contrato)
            ->with('sucesso', 'Apólice adicionada ao contrato.');
    }

    public function destroy(ContratoSeguroFianca $seguro): RedirectResponse
    {
        $contrato = $seguro->contrato;
        $seguro->delete();

        return redirect()
            ->route('contratos.show', $contrato)
            ->with('sucesso', 'Apólice removida.');
    }
}
