<?php

namespace App\Models;

use App\Enums\CategoriaArmazenamento;
use App\Enums\TipoDocumento;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Documento extends Model
{
    use HasFactory;

    protected $fillable = [
        'documentavel_id', 'documentavel_type', 'tipo_documento', 'categoria_armazenamento',
        'nome_original', 'caminho_arquivo', 'mime_type', 'tamanho_bytes', 'descricao',
        'enviado_por_user_id',
    ];

    protected $casts = [
        'tipo_documento' => TipoDocumento::class,
        'categoria_armazenamento' => CategoriaArmazenamento::class,
    ];

    public function documentavel(): MorphTo
    {
        return $this->morphTo();
    }

    public function enviadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enviado_por_user_id');
    }

    public function urlDownload(): string
    {
        return route('documentos.download', $this);
    }

    public function removerArquivo(): bool
    {
        if ($this->caminho_arquivo && Storage::disk('privada')->exists($this->caminho_arquivo)) {
            return Storage::disk('privada')->delete($this->caminho_arquivo);
        }

        return false;
    }
}
