<?php

namespace App\Http\Controllers;

use App\Enums\EstadoCivil;
use App\Enums\TipoVinculo;
use App\Http\Requests\PessoaRequest;
use App\Models\Pessoa;
use App\Models\PessoaRelacionamento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PessoaController extends Controller
{
    public function index(Request $request): View
    {
        $busca = $request->input('busca', '');

        $pessoas = Pessoa::query()
            ->when($busca, fn ($query, $busca) => $query->where(function ($q) use ($busca) {
                $termo = "%{$busca}%";
                $q->where('nome', 'like', $termo)
                    ->orWhere('cpf_cnpj', 'like', $termo)
                    ->orWhere('email', 'like', $termo)
                    ->orWhere('cidade', 'like', $termo);
            }))
            ->orderBy('nome')
            ->paginate(10)
            ->withQueryString();

        return view('pessoas.index', compact('pessoas', 'busca'));
    }

    public function create(): View
    {
        return view('pessoas.create');
    }

    public function store(PessoaRequest $request): RedirectResponse
    {
        $pessoa = Pessoa::create($request->validated());

        $this->salvarRelacionamento($pessoa, $request);

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
        $pessoa->load('relacionamentos');

        return view('pessoas.edit', compact('pessoa'));
    }

    public function update(PessoaRequest $request, Pessoa $pessoa): RedirectResponse
    {
        $pessoa->update($request->validated());

        $this->salvarRelacionamento($pessoa, $request);

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

    public function buscar(Request $request): JsonResponse
    {
        $busca = $request->input('busca', '');
        $excluir = $request->input('excluir');

        $query = Pessoa::query()->orderBy('nome');

        if ($busca) {
            $termo = "%{$busca}%";
            $query->where(function ($q) use ($termo) {
                $q->where('nome', 'like', $termo)
                    ->orWhere('cpf_cnpj', 'like', $termo);
            });
        }

        if ($excluir) {
            $query->where('id', '!=', $excluir);
        }

        return response()->json($query->limit(50)->get(['id', 'nome', 'cpf_cnpj']));
    }

    public function storeAjax(PessoaRequest $request): JsonResponse
    {
        $pessoa = Pessoa::create($request->validated());

        return response()->json($pessoa->only('id', 'nome', 'cpf_cnpj'));
    }

    private function salvarRelacionamento(Pessoa $pessoa, PessoaRequest $request): void
    {
        $estadoCivil = $request->enum('estado_civil', EstadoCivil::class);
        $estadosComVinculo = [EstadoCivil::CASADO, EstadoCivil::UNIAO_ESTAVEL];

        if (in_array($estadoCivil, $estadosComVinculo) && $request->filled('conjuge_id')) {
            if ($request->input('conjuge_id') != $pessoa->id) {
                PessoaRelacionamento::updateOrCreate(
                    ['pessoa_id' => $pessoa->id],
                    [
                        'conjuge_id' => $request->input('conjuge_id'),
                        'tipo_vinculo' => $estadoCivil === EstadoCivil::CASADO
                            ? TipoVinculo::CONJUGE
                            : TipoVinculo::COMPANHEIRO_UNIAO_ESTAVEL,
                    ]
                );
            }
        } else {
            $pessoa->relacionamentos()->delete();
        }
    }
}
