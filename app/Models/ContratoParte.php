<?php

namespace App\Models;

use App\Enums\PapelContrato;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContratoParte extends Model
{
    use HasFactory;

    protected $table = 'contrato_partes';

    protected $fillable = ['contrato_id', 'pessoa_id', 'papel', 'assina_contrato'];

    protected $casts = [
        'papel' => PapelContrato::class,
        'assina_contrato' => 'boolean',
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class);
    }

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class)->withTrashed();
    }
}
