<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Corretor extends Model
{
    use HasFactory;
    
    protected $table = 'corretores';

    protected $fillable = ['pessoa_id', 'user_id', 'creci', 'percentual_comissao', 'ativo'];

    protected $casts = [
        'percentual_comissao' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Carteira de imóveis atendidos por este corretor. */
    public function imoveis(): BelongsToMany
    {
        return $this->belongsToMany(Imovel::class, 'corretor_imoveis')
            ->withPivot('data_atribuicao', 'data_fim', 'ativo')
            ->withTimestamps();
    }
}
