<?php

namespace App\Http\Controllers;

use App\Http\Requests\PessoaRequest;
use App\Models\Pessoa;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PessoaController extends Controller
{
    public function index(): View
    {
        $pessoas = Pessoa::query()
            ->orderBy('nome')
            ->paginate(10);

        return view('pessoas.index', compact('pessoas'));
    }

    public function create(): View
    {
        return view('pessoas.create');
    }

    public function store(PessoaRequest $request): RedirectResponse
    {
        Pessoa::create($request->validated());

        return redirect()
            ->route('pessoas.index')
            ->with('sucesso', 'Pessoa cadastrada com sucesso.');
    }

    public function show(Pessoa $pessoa): View
    {
        $pessoa->load('imoveis', 'contratos', 'corretor', 'relacionamentos.conjuge');

        return view('pessoas.show', compact('pessoa'));
    }

    public function edit(Pessoa $pessoa): View
    {
        return view('pessoas.edit', compact('pessoa'));
    }

    public function update(PessoaRequest $request, Pessoa $pessoa): RedirectResponse
    {
        $pessoa->update($request->validated());

        return redirect()
            ->route('pessoas.index')
            ->with('sucesso', 'Pessoa atualizada com sucesso.');
    }

    public function destroy(Pessoa $pessoa): RedirectResponse
    {
        $pessoa->delete();

        return redirect()
            ->route('pessoas.index')
            ->with('sucesso', 'Pessoa removida com sucesso.');
    }
}
