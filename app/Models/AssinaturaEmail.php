<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssinaturaEmail extends Model
{
    protected $table = 'assinaturas_email';

    protected $fillable = [
        'nome',
        'conteudo',
        'ativo',
        'sistema',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'sistema' => 'boolean',
    ];

    public function modelos(): HasMany
    {
        return $this->hasMany(ModeloEmail::class, 'assinatura_email_id');
    }

    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where('ativo', true);
    }

    public function scopeOrdenados(Builder $query): Builder
    {
        return $query->orderBy('nome');
    }

    public static function sanitizarConteudo(string $html): string
    {
        $permitidas = '<p><br><strong><b><em><i><u><s><a><span><div><hr><img>';

        $html = strip_tags($html, $permitidas);
        $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = preg_replace('/javascript\s*:/i', '', $html);
        $html = preg_replace('/\s(src|href)\s*=\s*(?:"data:[^"]*"|\'data:[^\']*\'|data:[^\s>]+)/i', '', $html);

        return $html;
    }
}
