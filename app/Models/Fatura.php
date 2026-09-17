<?php

namespace App\Models;

use App\Enums\StatusPagamento;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Fatura extends Model
{
    use HasFactory;

    protected $fillable = [
        'contrato_id', 'referencia', 'data_vencimento', 'valor_aluguel', 'valor_condominio',
        'valor_iptu', 'parcela_iptu', 'valor_seguro', 'valor_taxa_extra', 'descricao_taxa_extra',
        'valor_desconto', 'descricao_desconto', 'valor_multa_juros', 'valor_pago', 'dias_atraso',
        'status_pagamento', 'data_recebimento', 'banco_recebedor', 'descricao_boleto',
    ];

    protected $casts = [
        'referencia' => 'date',
        'data_vencimento' => 'date',
        'data_recebimento' => 'date',
        'status_pagamento' => StatusPagamento::class,
        'valor_aluguel' => 'decimal:2',
        'valor_condominio' => 'decimal:2',
        'valor_iptu' => 'decimal:2',
        'valor_seguro' => 'decimal:2',
        'valor_taxa_extra' => 'decimal:2',
        'valor_desconto' => 'decimal:2',
        'valor_multa_juros' => 'decimal:2',
        'valor_pago' => 'decimal:2',
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class);
    }

    public function repasse(): HasOne
    {
        return $this->hasOne(Repasse::class);
    }

    public function documentos(): MorphMany
    {
        return $this->morphMany(Documento::class, 'documentavel');
    }
}
