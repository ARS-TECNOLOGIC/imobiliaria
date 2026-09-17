<?php

namespace App\Models;

use App\Enums\TipoVinculo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PessoaRelacionamento extends Model
{
    use HasFactory;

    protected $fillable = ['pessoa_id', 'conjuge_id', 'tipo_vinculo'];

    protected $casts = [
        'tipo_vinculo' => TipoVinculo::class,
    ];

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }

    public function conjuge(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'conjuge_id');
    }
}
