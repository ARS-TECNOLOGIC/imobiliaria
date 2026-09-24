<?php

namespace App\Http\Controllers;

use App\Models\ModeloEmail;
use App\Models\ModeloEmailAnexo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ModeloEmailAnexoController extends Controller
{
    public function store(Request $request, ModeloEmail $modelo): RedirectResponse
    {
        $request->validate([
            'arquivo' => ['required', 'file', 'max:10240', 'mimes:pdf,jpeg,jpg,png,gif,webp,doc,docx,xls,xlsx,zip,txt'],
        ]);

        $arquivo = $request->file('arquivo');
        $pasta = 'emails/modelos/'.$modelo->id;
        $nomeArquivo = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $arquivo->getClientOriginalName());

        $caminho = $arquivo->storeAs($pasta, $nomeArquivo, 'privada');

        if (! $caminho) {
            return back()->with('erro', 'Não foi possível enviar o arquivo.');
        }

        ModeloEmailAnexo::create([
            'modelo_email_id' => $modelo->id,
            'nome_original' => $arquivo->getClientOriginalName(),
            'caminho_arquivo' => $caminho,
            'mime_type' => $arquivo->getMimeType(),
            'tamanho_bytes' => $arquivo->getSize(),
        ]);

        return redirect()
            ->route('configuracoes.emails.edit', $modelo)
            ->with('sucesso', 'Anexo enviado com sucesso.');
    }

    public function download(ModeloEmail $modelo, ModeloEmailAnexo $anexo): StreamedResponse
    {
        abort_unless($anexo->modelo_email_id === $modelo->id, 404);
        abort_unless(Storage::disk('privada')->exists($anexo->caminho_arquivo), 404);

        return Storage::disk('privada')->download(
            $anexo->caminho_arquivo,
            $anexo->nome_original
        );
    }

    public function destroy(ModeloEmail $modelo, ModeloEmailAnexo $anexo): RedirectResponse
    {
        abort_unless($anexo->modelo_email_id === $modelo->id, 404);

        $anexo->removerArquivo();
        $anexo->delete();

        return redirect()
            ->route('configuracoes.emails.edit', $modelo)
            ->with('sucesso', 'Anexo removido com sucesso.');
    }
}
