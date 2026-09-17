<?php

namespace App\Models;

use App\Enums\TipoCampoHistorico;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContratoHistoricoValor extends Model
{
    use HasFactory;

    protected $table = 'contrato_historico_valores';

    protected $fillable = [
        'contrato_id', 'tipo_campo', 'valor_anterior', 'valor_novo', 'motivo', 'alterado_por_user_id',
    ];

    protected $casts = [
        'tipo_campo' => TipoCampoHistorico::class,
        'valor_anterior' => 'decimal:2',
        'valor_novo' => 'decimal:2',
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class);
    }

    public function alteradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alterado_por_user_id');
    }
}
