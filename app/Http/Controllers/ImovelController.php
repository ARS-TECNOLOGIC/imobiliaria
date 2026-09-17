<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImovelRequest;
use App\Models\Imovel;
use App\Models\Pessoa;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ImovelController extends Controller
{
    public function index(): View
    {
        $imoveis = Imovel::query()
            ->with('locador')
            ->orderBy('codigo')
            ->paginate(15);

        return view('imoveis.index', compact('imoveis'));
    }

    public function create(): View
    {
        $locadores = Pessoa::orderBy('nome')->get(['id', 'nome']);

        return view('imoveis.create', compact('locadores'));
    }

    public function store(ImovelRequest $request): RedirectResponse
    {
        $dados = $request->validated();
        // Checkbox desmarcado não vem no POST, então garantimos o valor explícito.
        $dados['possui_condominio'] = $request->boolean('possui_condominio');

        Imovel::create($dados);

        return redirect()
            ->route('imoveis.index')
            ->with('sucesso', 'Imóvel cadastrado com sucesso.');
    }

    public function show(Imovel $imovel): View
    {
        $imovel->load('locador', 'servicosCondominio', 'corretores', 'contratos.faturas');

        return view('imoveis.show', compact('imovel'));
    }

    public function edit(Imovel $imovel): View
    {
        $locadores = Pessoa::orderBy('nome')->get(['id', 'nome']);

        return view('imoveis.edit', compact('imovel', 'locadores'));
    }

    public function update(ImovelRequest $request, Imovel $imovel): RedirectResponse
    {
        $dados = $request->validated();
        $dados['possui_condominio'] = $request->boolean('possui_condominio');

        $imovel->update($dados);

        return redirect()
            ->route('imoveis.index')
            ->with('sucesso', 'Imóvel atualizado com sucesso.');
    }

    public function destroy(Imovel $imovel): RedirectResponse
    {
        $imovel->delete();

        return redirect()
            ->route('imoveis.index')
            ->with('sucesso', 'Imóvel removido com sucesso.');
    }
}
