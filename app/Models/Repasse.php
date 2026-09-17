<?php

namespace App\Models;

use App\Enums\StatusRepasse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Repasse extends Model
{
    use HasFactory;

    protected $fillable = [
        'fatura_id', 'valor_taxa_adm', 'valor_retido', 'descricao_retido', 'valor_custos',
        'descricao_custos', 'valor_total_repasse', 'data_limite_repasse', 'data_repasse', 'status',
    ];

    protected $casts = [
        'status' => StatusRepasse::class,
        'data_limite_repasse' => 'date',
        'data_repasse' => 'date',
        'valor_taxa_adm' => 'decimal:2',
        'valor_retido' => 'decimal:2',
        'valor_custos' => 'decimal:2',
        'valor_total_repasse' => 'decimal:2',
    ];

    public function fatura(): BelongsTo
    {
        return $this->belongsTo(Fatura::class);
    }
}
