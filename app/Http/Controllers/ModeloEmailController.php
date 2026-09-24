<?php

namespace App\Http\Controllers;

use App\Models\AssinaturaEmail;
use App\Models\ModeloEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ModeloEmailController extends Controller
{
    public function index(): View
    {
        $modelos = ModeloEmail::ordenados()->get()->groupBy('escopo');
        $escopos = ModeloEmail::escoposDisponiveis();
        $assinaturas = AssinaturaEmail::ordenados()->get();

        return view('configuracoes.emails.index', compact('modelos', 'escopos', 'assinaturas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:150', Rule::unique('modelos_email', 'nome')],
            'escopo' => ['required', 'string', 'max:100', Rule::in(array_keys(ModeloEmail::escoposDisponiveis()))],
            'remetente_email' => ['required', 'email', 'max:150'],
            'remetente_nome' => ['required', 'string', 'max:100'],
            'assunto' => ['required', 'string', 'max:255'],
            'corpo' => ['required', 'string'],
            'ativo' => ['boolean'],
            'descricao' => ['nullable', 'string', 'max:255'],
            'assinatura_email_id' => ['nullable', 'integer', Rule::exists('assinaturas_email', 'id')],
        ]);

        $validated['slug'] = ModeloEmail::gerarSlug($validated['nome']);
        $validated['ativo'] = $request->boolean('ativo');
        $validated['corpo'] = $this->sanitizarCorpo($validated['corpo']);
        $validated['assinatura_email_id'] = ($validated['assinatura_email_id'] ?? null) ?: null;

        ModeloEmail::create($validated);

        return redirect()->route('configuracoes.index')
            ->with('sucesso', 'Modelo de e-mail criado com sucesso.')
            ->with('aba', 'emails');
    }

    public function show(ModeloEmail $modelo): View
    {
        $variaveisEscopo = ModeloEmail::variaveisPorEscopo($modelo->escopo);
        $modelo->load(['anexos', 'assinatura']);

        return view('configuracoes.emails.show', compact('modelo', 'variaveisEscopo'));
    }

    public function edit(ModeloEmail $modelo): View
    {
        $escopos = ModeloEmail::escoposDisponiveis();
        $variaveisEscopo = ModeloEmail::variaveisPorEscopo($modelo->escopo);
        $modelo->load(['anexos', 'assinatura']);
        $assinaturas = AssinaturaEmail::ordenados()->get();

        return view('configuracoes.emails.edit', compact('modelo', 'escopos', 'variaveisEscopo', 'assinaturas'));
    }

    public function update(Request $request, ModeloEmail $modelo): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:150', Rule::unique('modelos_email', 'nome')->ignore($modelo->id)],
            'escopo' => ['required', 'string', 'max:100', Rule::in(array_keys(ModeloEmail::escoposDisponiveis()))],
            'remetente_email' => ['required', 'email', 'max:150'],
            'remetente_nome' => ['required', 'string', 'max:100'],
            'assunto' => ['required', 'string', 'max:255'],
            'corpo' => ['required', 'string'],
            'ativo' => ['boolean'],
            'descricao' => ['nullable', 'string', 'max:255'],
            'assinatura_email_id' => ['nullable', 'integer', Rule::exists('assinaturas_email', 'id')],
        ]);

        $validated['slug'] = ModeloEmail::gerarSlug($validated['nome']);
        $validated['ativo'] = $modelo->sistema ? true : $request->boolean('ativo');
        $validated['corpo'] = $this->sanitizarCorpo($validated['corpo']);
        $validated['assinatura_email_id'] = ($validated['assinatura_email_id'] ?? null) ?: null;

        $corpoAntigo = $modelo->corpo;
        $modelo->update($validated);
        $this->removerImagensSubstituidas($corpoAntigo, $modelo->corpo);

        return redirect()->route('configuracoes.index')
            ->with('sucesso', 'Modelo de e-mail atualizado com sucesso.')
            ->with('aba', 'emails');
    }

    public function destroy(ModeloEmail $modelo): RedirectResponse
    {
        if ($modelo->sistema) {
            return back()->with('erro', 'Não é permitido excluir modelos de sistema.');
        }

        $modelo->load('anexos');
        foreach ($modelo->anexos as $anexo) {
            $anexo->removerArquivo();
        }

        $imagens = $this->extrairCaminhosImagens($modelo->corpo);
        $modelo->delete();
        $this->removerImagensSeNaoReferenciadas($imagens);

        return redirect()->route('configuracoes.index')
            ->with('sucesso', 'Modelo de e-mail removido com sucesso.')
            ->with('aba', 'emails');
    }

    public function variaveisPorEscopo(Request $request): JsonResponse
    {
        $escopo = $request->get('escopo');
        $variaveis = ModeloEmail::variaveisPorEscopo($escopo);

        return response()->json($variaveis);
    }

    public function uploadImagem(Request $request): JsonResponse
    {
        $request->validate([
            'imagem' => ['required', 'file', 'max:2048', 'mimes:jpeg,jpg,png,gif,webp'],
        ]);

        $arquivo = $request->file('imagem');
        $pasta = 'emails/imagens';
        $nome = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $arquivo->getClientOriginalName());
        $caminho = $arquivo->storeAs($pasta, $nome, 'public');

        if (! $caminho) {
            return response()->json(['ok' => false, 'erro' => 'Falha ao enviar a imagem.'], 500);
        }

        return response()->json([
            'ok' => true,
            'url' => '/storage/'.$caminho,
        ]);
    }

    private function sanitizarCorpo(string $html): string
    {
        $permitidas = '<p><br><strong><b><em><i><u><s><ul><ol><li><h1><h2><h3><h4><h5><h6>'
            .'<a><span><div><blockquote><hr><sub><sup><table><thead><tbody><tr><th><td><img>';

        $html = strip_tags($html, $permitidas);
        $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = preg_replace('/javascript\s*:/i', '', $html);
        $html = preg_replace('/\s(src|href)\s*=\s*(?:"data:[^"]*"|\'data:[^\']*\'|data:[^\s>]+)/i', '', $html);

        return $html;
    }

    private function removerImagensSubstituidas(string $corpoAntigo, string $corpoNovo): void
    {
        if ($corpoAntigo === $corpoNovo) {
            return;
        }

        $antes = $this->extrairCaminhosImagens($corpoAntigo);
        $depois = $this->extrairCaminhosImagens($corpoNovo);

        $this->removerImagensSeNaoReferenciadas(array_values(array_diff($antes, $depois)));
    }

    /**
     * @param  list<string>  $caminhos
     */
    private function removerImagensSeNaoReferenciadas(array $caminhos): void
    {
        foreach ($caminhos as $caminho) {
            $referenciada = ModeloEmail::where('corpo', 'like', '%'.$caminho.'%')->exists();

            if (! $referenciada) {
                Storage::disk('public')->delete($caminho);
            }
        }
    }

    /**
     * @return list<string>
     */
    private function extrairCaminhosImagens(string $html): array
    {
        preg_match_all('#(?:/storage/)?(emails/imagens/[^"\'\s>]+)#', $html, $matches);

        return array_values(array_unique($matches[1]));
    }
}
