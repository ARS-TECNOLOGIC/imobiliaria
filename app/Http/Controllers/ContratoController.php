<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContratoRequest;
use App\Models\Contrato;
use App\Models\Imovel;
use App\Models\Pessoa;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContratoController extends Controller
{
    public function index(): View
    {
        $contratos = Contrato::query()
            ->with('imovel', 'favorecido')
            ->orderByDesc('id')
            ->paginate(15);

        return view('contratos.index', compact('contratos'));
    }

    public function create(): View
    {
        [$imoveis, $pessoas] = $this->opcoesFormulario();

        return view('contratos.create', compact('imoveis', 'pessoas'));
    }

    public function store(ContratoRequest $request): RedirectResponse
    {
        $contrato = Contrato::create($request->validated());

        return redirect()
            ->route('contratos.show', $contrato)
            ->with('sucesso', 'Contrato cadastrado com sucesso. Agora adicione as partes (locador, locatário, fiador).');
    }

    public function show(Contrato $contrato): View
    {
        $contrato->load('imovel.locador', 'favorecido', 'partes.pessoa', 'segurosFiancas', 'faturas');

        return view('contratos.show', compact('contrato'));
    }

    public function edit(Contrato $contrato): View
    {
        [$imoveis, $pessoas] = $this->opcoesFormulario();

        return view('contratos.edit', compact('contrato', 'imoveis', 'pessoas'));
    }

    public function update(ContratoRequest $request, Contrato $contrato): RedirectResponse
    {
        $contrato->update($request->validated());

        return redirect()
            ->route('contratos.show', $contrato)
            ->with('sucesso', 'Contrato atualizado com sucesso.');
    }

    public function destroy(Contrato $contrato): RedirectResponse
    {
        $contrato->delete();

        return redirect()
            ->route('contratos.index')
            ->with('sucesso', 'Contrato removido com sucesso.');
    }

    /** @return array{0: \Illuminate\Support\Collection, 1: \Illuminate\Support\Collection} */
    private function opcoesFormulario(): array
    {
        $imoveis = Imovel::orderBy('codigo')->get(['id', 'codigo', 'logradouro', 'numero', 'locador_id']);
        $pessoas = Pessoa::orderBy('nome')->get(['id', 'nome']);

        return [$imoveis, $pessoas];
    }
}
