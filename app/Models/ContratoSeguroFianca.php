<?php

namespace App\Models;

use App\Enums\StatusSeguro;
use App\Enums\TipoSeguro;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContratoSeguroFianca extends Model
{
    use HasFactory;

    protected $table = 'contrato_seguros_fiancas';

    protected $fillable = [
        'contrato_id', 'tipo', 'seguradora', 'numero_apolice', 'valor_cobertura',
        'valor_premio', 'data_inicio', 'data_vencimento_apolice', 'status',
    ];

    protected $casts = [
        'tipo' => TipoSeguro::class,
        'status' => StatusSeguro::class,
        'data_inicio' => 'date',
        'data_vencimento_apolice' => 'date',
        'valor_cobertura' => 'decimal:2',
        'valor_premio' => 'decimal:2',
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class);
    }
}
