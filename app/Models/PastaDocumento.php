<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PastaDocumento extends Model
{
    protected $table = 'pastas_documentos';

    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'ativa',
        'ordem',
        'sistema',
    ];

    protected $casts = [
        'ativa' => 'boolean',
        'sistema' => 'boolean',
        'ordem' => 'integer',
    ];

    public function scopeAtivas($query)
    {
        return $query->where('ativa', true);
    }

    public function scopeOrdenadas($query)
    {
        return $query->orderBy('ordem')->orderBy('nome');
    }

    public static function gerarSlug(string $nome): string
    {
        return str()->slug($nome);
    }
}
