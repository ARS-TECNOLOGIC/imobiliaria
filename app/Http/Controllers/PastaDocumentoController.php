<?php

namespace App\Http\Controllers;

use App\Models\PastaDocumento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PastaDocumentoController extends Controller
{
    public function index(): View
    {
        $pastas = PastaDocumento::ordenadas()->get();

        return view('configuracoes.documentos.index', compact('pastas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:100', Rule::unique('pastas_documentos', 'nome')],
            'descricao' => ['nullable', 'string'],
            'ativa' => ['boolean'],
            'ordem' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['slug'] = PastaDocumento::gerarSlug($validated['nome']);
        $validated['ativa'] = $request->boolean('ativa');
        $validated['ordem'] = $validated['ordem'] ?? PastaDocumento::max('ordem') + 1;

        PastaDocumento::create($validated);

        return redirect()->route('configuracoes.index')
            ->with('sucesso', 'Pasta criada com sucesso.')
            ->with('aba', 'documentos');
    }

    public function update(Request $request, PastaDocumento $pasta): RedirectResponse
    {
        if ($pasta->sistema && $request->boolean('ativa') === false) {
            return back()->withErrors(['ativa' => 'Não é permitido desativar uma pasta de sistema.'])
                ->withInput();
        }

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:100', Rule::unique('pastas_documentos', 'nome')->ignore($pasta->id)],
            'descricao' => ['nullable', 'string'],
            'ativa' => ['boolean'],
            'ordem' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['slug'] = PastaDocumento::gerarSlug($validated['nome']);
        $validated['ativa'] = $request->boolean('ativa');

        $pasta->update($validated);

        return redirect()->route('configuracoes.index')
            ->with('sucesso', 'Pasta atualizada com sucesso.')
            ->with('aba', 'documentos');
    }

    public function destroy(PastaDocumento $pasta): RedirectResponse
    {
        if ($pasta->sistema) {
            return back()->with('erro', 'Não é permitido excluir pastas de sistema.');
        }

        $pasta->delete();

        $this->resequenciarOrdens();

        return redirect()->route('configuracoes.index')
            ->with('sucesso', 'Pasta removida com sucesso.')
            ->with('aba', 'documentos');
    }

    public function reordenar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ordem' => ['required', 'array'],
            'ordem.*' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($validated['ordem'] as $id => $ordem) {
            PastaDocumento::where('id', $id)->update(['ordem' => $ordem]);
        }

        return redirect()->route('configuracoes.index')
            ->with('sucesso', 'Ordem das pastas atualizada com sucesso.')
            ->with('aba', 'documentos');
    }

    private function resequenciarOrdens(): void
    {
        PastaDocumento::ordenadas()->get()->each(function (PastaDocumento $pasta, int $indice) {
            $novaOrdem = $indice + 1;
            if ($pasta->ordem !== $novaOrdem) {
                $pasta->update(['ordem' => $novaOrdem]);
            }
        });
    }
}
