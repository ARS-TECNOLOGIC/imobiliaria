<?php

namespace App\Http\Controllers;

use App\Models\AssinaturaEmail;
use App\Models\Configuracao;
use App\Models\ModeloEmail;
use App\Models\PastaDocumento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ConfiguracaoController extends Controller
{
    protected const GRUPOS_SISTEMA = [
        'iptu' => 'IPTU',
        'contrato' => 'Contratos',
        'financeiro' => 'Financeiro',
        'outros' => 'Outros',
    ];

    public function index(): View
    {
        $configuracoes = Configuracao::orderBy('grupo')->orderBy('chave')->get()
            ->groupBy('grupo');

        $gruposOrdenados = [];
        foreach (self::GRUPOS_SISTEMA as $chave => $nome) {
            if ($configuracoes->has($chave)) {
                $gruposOrdenados[$chave] = $configuracoes[$chave];
            }
        }
        // Adiciona grupos não mapeados
        foreach ($configuracoes->keys() as $grupo) {
            if (! isset($gruposOrdenados[$grupo])) {
                $gruposOrdenados[$grupo] = $configuracoes[$grupo];
            }
        }

        $pastas = PastaDocumento::ordenadas()->get();
        $modelos = ModeloEmail::ordenados()->get()->groupBy('escopo');
        $escopos = ModeloEmail::escoposDisponiveis();
        $assinaturas = AssinaturaEmail::ordenados()->get();

        return view('configuracoes.index', [
            'configuracoesPorGrupo' => collect($gruposOrdenados),
            'gruposSistema' => self::GRUPOS_SISTEMA,
            'pastas' => $pastas,
            'modelos' => $modelos,
            'escopos' => $escopos,
            'assinaturas' => $assinaturas,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'chave' => ['required', 'string', 'max:100', Rule::unique('configuracoes', 'chave')],
            'valor' => ['nullable', 'string'],
            'tipo' => ['required', Rule::in(['string', 'integer', 'decimal', 'boolean', 'json', 'array'])],
            'descricao' => ['nullable', 'string', 'max:255'],
            'grupo' => ['required', 'string', 'max:50'],
            'sistema' => ['boolean'],
        ]);

        $validated['valor'] = $this->converterValor($validated['valor'] ?? '', $validated['tipo']);
        $validated['sistema'] = $request->boolean('sistema');

        Configuracao::create($validated);

        return redirect()->route('configuracoes.index')
            ->with('sucesso', 'Configuração criada com sucesso.')
            ->with('aba', 'sistema');
    }

    public function update(Request $request, Configuracao $configuracao): RedirectResponse
    {
        if ($configuracao->sistema && $request->boolean('sistema') === false) {
            return back()->withErrors(['sistema' => 'Não é permitido alterar uma configuração de sistema para não-sistema.'])
                ->withInput();
        }

        $validated = $request->validate([
            'valor' => ['nullable', 'string'],
            'tipo' => ['required', Rule::in(['string', 'integer', 'decimal', 'boolean', 'json', 'array'])],
            'descricao' => ['nullable', 'string', 'max:255'],
            'grupo' => ['required', 'string', 'max:50'],
            'sistema' => ['boolean'],
        ]);

        $validated['valor'] = $this->converterValor($validated['valor'] ?? '', $validated['tipo']);
        $validated['sistema'] = $request->boolean('sistema');

        $configuracao->update($validated);

        return redirect()->route('configuracoes.index')
            ->with('sucesso', 'Configuração atualizada com sucesso.')
            ->with('aba', 'sistema');
    }

    public function destroy(Configuracao $configuracao): RedirectResponse
    {
        if ($configuracao->sistema) {
            return back()->with('erro', 'Não é permitido excluir configurações de sistema.');
        }

        $configuracao->delete();

        return redirect()->route('configuracoes.index')
            ->with('sucesso', 'Configuração removida com sucesso.')
            ->with('aba', 'sistema');
    }

    public function updateMultiplo(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'config' => ['nullable', 'array'],
            'config.*' => ['nullable', 'string'],
            'tipos' => ['nullable', 'array'],
            'tipos.*' => ['string', Rule::in(['string', 'integer', 'decimal', 'boolean', 'json', 'array'])],
        ]);

        $validated['config'] = $validated['config'] ?? [];

        foreach ($validated['config'] as $id => $valor) {
            $configuracao = Configuracao::find($id);
            if (! $configuracao || $configuracao->sistema) {
                continue;
            }

            $tipo = $validated['tipos'][$id] ?? $configuracao->tipo;
            $valorConvertido = $this->converterValor($valor ?? '', $tipo);

            $configuracao->update(['valor' => $valorConvertido]);
        }

        return redirect()->route('configuracoes.index')
            ->with('sucesso', 'Configurações atualizadas com sucesso.')
            ->with('aba', 'sistema');
    }

    private function converterValor(string $valor, string $tipo): mixed
    {
        if ($valor === '' || $valor === null) {
            return null;
        }

        return match ($tipo) {
            'integer' => (int) $valor,
            'decimal' => (float) $valor,
            'boolean' => filter_var($valor, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($valor, true) ?? $valor,
            'array' => json_decode($valor, true) ?? explode(',', $valor),
            default => $valor,
        };
    }
}
