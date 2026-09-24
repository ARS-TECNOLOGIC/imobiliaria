<?php

namespace App\Http\Controllers;

use App\Models\AssinaturaEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssinaturaEmailController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:150', Rule::unique('assinaturas_email', 'nome')],
            'conteudo' => ['required', 'string'],
            'ativo' => ['boolean'],
            'sistema' => ['boolean'],
        ]);

        $validated['conteudo'] = AssinaturaEmail::sanitizarConteudo($validated['conteudo']);
        $validated['ativo'] = $request->boolean('ativo');
        $validated['sistema'] = $request->boolean('sistema');

        AssinaturaEmail::create($validated);

        return redirect()->route('configuracoes.index')
            ->with('sucesso', 'Assinatura criada com sucesso.')
            ->with('aba', 'emails');
    }

    public function update(Request $request, AssinaturaEmail $assinatura): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:150', Rule::unique('assinaturas_email', 'nome')->ignore($assinatura->id)],
            'conteudo' => ['required', 'string'],
            'ativo' => ['boolean'],
        ]);

        $validated['conteudo'] = AssinaturaEmail::sanitizarConteudo($validated['conteudo']);
        $validated['sistema'] = $assinatura->sistema;
        $validated['ativo'] = $assinatura->sistema ? true : $request->boolean('ativo');

        $assinatura->update($validated);

        return redirect()->route('configuracoes.index')
            ->with('sucesso', 'Assinatura atualizada com sucesso.')
            ->with('aba', 'emails');
    }

    public function destroy(AssinaturaEmail $assinatura): RedirectResponse
    {
        if ($assinatura->sistema) {
            return back()->with('erro', 'Não é permitido excluir assinaturas de sistema.');
        }

        $assinatura->delete();

        return redirect()->route('configuracoes.index')
            ->with('sucesso', 'Assinatura removida com sucesso.')
            ->with('aba', 'emails');
    }
}
