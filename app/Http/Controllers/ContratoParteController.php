<?php

namespace App\Http\Controllers;

use App\Enums\PapelContrato;
use App\Models\Contrato;
use App\Models\ContratoParte;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContratoParteController extends Controller
{
    /** Adiciona uma parte (locador, locatário ou fiador) ao contrato. */
    public function store(Request $request, Contrato $contrato): RedirectResponse
    {
        $dados = $request->validate([
            'pessoa_id' => ['required', 'exists:pessoas,id'],
            'papel' => ['required', Rule::enum(PapelContrato::class)],
            'assina_contrato' => ['nullable', 'boolean'],
        ], [], [
            'pessoa_id' => 'pessoa',
        ]);

        // Evita duplicar a mesma pessoa no mesmo papel dentro do contrato
        // (a própria tabela tem esse unique, mas validamos antes pra dar uma mensagem clara).
        $jaExiste = $contrato->partes()
            ->where('pessoa_id', $dados['pessoa_id'])
            ->where('papel', $dados['papel'])
            ->exists();

        if ($jaExiste) {
            return back()->withErrors(['pessoa_id' => 'Essa pessoa já está cadastrada com esse papel neste contrato.']);
        }

        $contrato->partes()->create([
            'pessoa_id' => $dados['pessoa_id'],
            'papel' => $dados['papel'],
            'assina_contrato' => $request->boolean('assina_contrato', true),
        ]);

        return redirect()
            ->route('contratos.show', $contrato)
            ->with('sucesso', 'Parte adicionada ao contrato.');
    }

    public function destroy(ContratoParte $parte): RedirectResponse
    {
        $contrato = $parte->contrato;
        $parte->delete();

        return redirect()
            ->route('contratos.show', $contrato)
            ->with('sucesso', 'Parte removida do contrato.');
    }
}
