<?php

namespace App\Models;

use App\Enums\ServicoCondominio;
use App\Enums\TipoCobranca;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImovelServicoCondominio extends Model
{
    use HasFactory;

    protected $table = 'imovel_servicos_condominio';

    protected $fillable = [
        'imovel_id', 'servico', 'descricao_outro', 'tipo_cobranca', 'observacao',
    ];

    protected $casts = [
        'servico' => ServicoCondominio::class,
        'tipo_cobranca' => TipoCobranca::class,
    ];

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class);
    }
}
