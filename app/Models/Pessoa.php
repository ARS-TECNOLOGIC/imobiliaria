<?php

namespace App\Models;

use App\Enums\EstadoCivil;
use App\Enums\RegimeBens;
use App\Enums\TipoConta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pessoa extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome', 'cpf_cnpj', 'rg', 'rg_orgao_emissor', 'data_nascimento',
        'estado_civil', 'regime_bens', 'email', 'telefone', 'celular',
        'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'uf',
        'tipo_chave_pix', 'chave_pix', 'banco', 'agencia', 'conta', 'tipo_conta',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'estado_civil' => EstadoCivil::class,
        'regime_bens' => RegimeBens::class,
        'tipo_conta' => TipoConta::class,
    ];

    /** Imóveis dos quais esta pessoa é locadora. */
    public function imoveis(): HasMany
    {
        return $this->hasMany(Imovel::class, 'locador_id');
    }

    /** Contratos em que esta pessoa é a favorecida do repasse. */
    public function contratosComoFavorecido(): HasMany
    {
        return $this->hasMany(Contrato::class, 'favorecido_id');
    }

    /** Todos os contratos em que a pessoa participa, em qualquer papel. */
    public function contratos(): BelongsToMany
    {
        return $this->belongsToMany(Contrato::class, 'contrato_partes')
            ->withPivot('papel', 'assina_contrato')
            ->withTimestamps();
    }

    public function relacionamentos(): HasMany
    {
        return $this->hasMany(PessoaRelacionamento::class, 'pessoa_id');
    }

    public function corretor(): HasOne
    {
        return $this->hasOne(Corretor::class, 'pessoa_id');
    }

    public function documentos(): MorphMany
    {
        return $this->morphMany(Documento::class, 'documentavel');
    }
}
