<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ModeloEmailAnexo extends Model
{
    protected $table = 'modelo_email_anexos';

    protected $fillable = [
        'modelo_email_id',
        'nome_original',
        'caminho_arquivo',
        'mime_type',
        'tamanho_bytes',
    ];

    public function modeloEmail(): BelongsTo
    {
        return $this->belongsTo(ModeloEmail::class, 'modelo_email_id');
    }

    public function urlDownload(): string
    {
        return route('configuracoes.emails.anexos.download', [$this->modelo_email_id, $this]);
    }

    public function removerArquivo(): bool
    {
        if ($this->caminho_arquivo && Storage::disk('privada')->exists($this->caminho_arquivo)) {
            return Storage::disk('privada')->delete($this->caminho_arquivo);
        }

        return false;
    }

    public function tamanhoFormatado(): string
    {
        if (! $this->tamanho_bytes) {
            return '—';
        }

        $unidades = ['B', 'KB', 'MB', 'GB'];
        $indice = 0;
        $tamanho = (float) $this->tamanho_bytes;

        while ($tamanho >= 1024 && $indice < count($unidades) - 1) {
            $tamanho /= 1024;
            $indice++;
        }

        return round($tamanho, 1).' '.$unidades[$indice];
    }
}
