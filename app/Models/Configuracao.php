<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracao extends Model
{
    protected $table = 'configuracoes';

    protected $fillable = [
        'chave',
        'valor',
        'tipo',
        'descricao',
        'grupo',
        'sistema',
    ];

    protected $casts = [
        'valor' => 'json',
        'sistema' => 'boolean',
    ];
}
