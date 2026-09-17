<?php

namespace App\Models;

use App\Enums\Garantia;
use App\Enums\PapelContrato;
use App\Enums\StatusContrato;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Contrato extends Model
{
    use HasFactory;

    protected $fillable = [
        'imovel_id', 'favorecido_id', 'data_inicio', 'data_fim', 'dia_vencimento',
        'garantia', 'valor_aluguel', 'valor_condominio', 'valor_iptu', 'parcela_iptu',
        'valor_seguro', 'taxa_adm_percentual', 'multa_percentual', 'status',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'garantia' => Garantia::class,
        'status' => StatusContrato::class,
        'valor_aluguel' => 'decimal:2',
        'valor_condominio' => 'decimal:2',
        'valor_iptu' => 'decimal:2',
        'valor_seguro' => 'decimal:2',
        'taxa_adm_percentual' => 'decimal:2',
        'multa_percentual' => 'decimal:2',
    ];

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class);
    }

    public function favorecido(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'favorecido_id')->withTrashed();
    }

    public function partes(): HasMany
    {
        return $this->hasMany(ContratoParte::class);
    }

    /** Todas as pessoas ligadas ao contrato, em qualquer papel. */
    public function pessoas(): BelongsToMany
    {
        return $this->belongsToMany(Pessoa::class, 'contrato_partes')
            ->withPivot('papel', 'assina_contrato')
            ->withTimestamps();
    }

    public function locadores(): BelongsToMany
    {
        return $this->pessoas()->wherePivotIn('papel', [
            PapelContrato::LOCADOR_TITULAR->value,
            PapelContrato::LOCADOR_CONJUGE->value,
        ]);
    }

    public function locatarios(): BelongsToMany
    {
        return $this->pessoas()->wherePivotIn('papel', [
            PapelContrato::LOCATARIO_TITULAR->value,
            PapelContrato::LOCATARIO_CONJUGE->value,
        ]);
    }

    public function fiadores(): BelongsToMany
    {
        return $this->pessoas()->wherePivotIn('papel', [
            PapelContrato::FIADOR_TITULAR->value,
            PapelContrato::FIADOR_CONJUGE->value,
        ]);
    }

    public function historicoValores(): HasMany
    {
        return $this->hasMany(ContratoHistoricoValor::class);
    }

    public function segurosFiancas(): HasMany
    {
        return $this->hasMany(ContratoSeguroFianca::class);
    }

    public function faturas(): HasMany
    {
        return $this->hasMany(Fatura::class);
    }

    public function documentos(): MorphMany
    {
        return $this->morphMany(Documento::class, 'documentavel');
    }
}
