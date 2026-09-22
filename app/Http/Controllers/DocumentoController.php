<?php

namespace App\Http\Controllers;

use App\Enums\CategoriaArmazenamento;
use App\Models\Contrato;
use App\Models\Documento;
use App\Models\Fatura;
use App\Models\Imovel;
use App\Models\Pessoa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentoController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'documentavel_type' => ['required', 'string', Rule::in([Pessoa::class, Imovel::class, Contrato::class, Fatura::class])],
            'documentavel_id' => ['required', 'integer'],
            'categoria_armazenamento' => ['required', Rule::enum(CategoriaArmazenamento::class)],
            'tipo_documento' => ['required', 'string', 'max:255'],
            'arquivo' => ['required', 'file', 'max:10240', 'mimes:pdf,jpeg,jpg,png'],
            'descricao' => ['nullable', 'string', 'max:255'],
        ]);

        $documentavelType = $request->input('documentavel_type');
        $documentavelId = $request->integer('documentavel_id');
        $categoria = CategoriaArmazenamento::from($request->input('categoria_armazenamento'));

        // Verifica se a entidade existe
        $documentavelType::findOrFail($documentavelId);

        $arquivo = $request->file('arquivo');
        $codigoEntidade = $this->obterCodigoEntidade($documentavelType, $documentavelId);

        // Monta o caminho: locacoes/{codigo}/{pasta}/{nome_final}
        $nomeArquivo = time().'_'.$arquivo->getClientOriginalName();
        $pasta = "locacoes/{$codigoEntidade}/{$categoria->pasta()}";

        $caminho = $arquivo->storeAs($pasta, $nomeArquivo, 'privada');

        $documento = Documento::create([
            'documentavel_type' => $documentavelType,
            'documentavel_id' => $documentavelId,
            'tipo_documento' => $request->input('tipo_documento'),
            'categoria_armazenamento' => $categoria->value,
            'nome_original' => $arquivo->getClientOriginalName(),
            'caminho_arquivo' => $caminho,
            'mime_type' => $arquivo->getMimeType(),
            'tamanho_bytes' => $arquivo->getSize(),
            'descricao' => $request->input('descricao'),
            'enviado_por_user_id' => auth()->id(),
        ]);

        return back()->with('sucesso', 'Documento enviado com sucesso.');
    }

    public function download(Documento $documento): StreamedResponse
    {
        abort_unless(Storage::disk('privada')->exists($documento->caminho_arquivo), 404);

        return Storage::disk('privada')->download(
            $documento->caminho_arquivo,
            $documento->nome_original
        );
    }

    public function destroy(Documento $documento): RedirectResponse
    {
        $documento->removerArquivo();
        $documento->delete();

        return back()->with('sucesso', 'Documento removido com sucesso.');
    }

    private function obterCodigoEntidade(string $type, int $id): string
    {
        return match ($type) {
            Contrato::class => 'LOC_'.str_pad($id, 5, '0', STR_PAD_LEFT),
            Pessoa::class => 'PESSOA_'.str_pad($id, 5, '0', STR_PAD_LEFT),
            Imovel::class => 'IMO_'.str_pad($id, 5, '0', STR_PAD_LEFT),
            Fatura::class => 'FAT_'.str_pad($id, 5, '0', STR_PAD_LEFT),
            default => 'OUTROS',
        };
    }
}
