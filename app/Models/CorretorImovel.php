<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorretorImovel extends Model
{
    use HasFactory;

    protected $table = 'corretor_imoveis';

    protected $fillable = ['corretor_id', 'imovel_id', 'data_atribuicao', 'data_fim', 'ativo'];

    protected $casts = [
        'data_atribuicao' => 'date',
        'data_fim' => 'date',
        'ativo' => 'boolean',
    ];

    public function corretor(): BelongsTo
    {
        return $this->belongsTo(Corretor::class);
    }

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class);
    }
}
