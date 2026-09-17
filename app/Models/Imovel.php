<?php

namespace App\Models;

use App\Enums\StatusContrato;
use App\Enums\TipoImovel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Imovel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'imoveis';

    protected $fillable = [
        'codigo', 'locador_id', 'tipo', 'cep', 'logradouro', 'numero',
        'complemento', 'bairro', 'cidade', 'uf', 'possui_condominio',
        'nome_condominio', 'administradora_condominio',
    ];

    protected $casts = [
        'tipo' => TipoImovel::class,
        'possui_condominio' => 'boolean',
    ];

    public function locador(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'locador_id')->withTrashed();
    }

    public function servicosCondominio(): HasMany
    {
        return $this->hasMany(ImovelServicoCondominio::class);
    }

    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class);
    }

    /** Contrato vigente do imóvel, se houver. */
    public function contratoAtivo(): HasOne
    {
        return $this->hasOne(Contrato::class)->where('status', StatusContrato::ATIVO->value);
    }

    public function corretores(): BelongsToMany
    {
        return $this->belongsToMany(Corretor::class, 'corretor_imoveis')
            ->withPivot('data_atribuicao', 'data_fim', 'ativo')
            ->withTimestamps();
    }

    public function documentos(): MorphMany
    {
        return $this->morphMany(Documento::class, 'documentavel');
    }
}
