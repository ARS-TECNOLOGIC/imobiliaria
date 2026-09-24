<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModeloEmail extends Model
{
    protected $table = 'modelos_email';

    protected $fillable = [
        'nome',
        'slug',
        'escopo',
        'remetente_email',
        'remetente_nome',
        'assunto',
        'corpo',
        'ativo',
        'sistema',
        'descricao',
        'assinatura_email_id',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'sistema' => 'boolean',
    ];

    public function anexos(): HasMany
    {
        return $this->hasMany(ModeloEmailAnexo::class, 'modelo_email_id');
    }

    public function assinatura(): BelongsTo
    {
        return $this->belongsTo(AssinaturaEmail::class, 'assinatura_email_id');
    }

    public function corpoComAssinatura(): string
    {
        $assinatura = $this->assinatura;

        if (! $assinatura || ! $assinatura->ativo) {
            return $this->corpo;
        }

        return $this->corpo.'<hr style="border:none;border-top:1px solid #dee2e6;margin:1.25em 0;">'
            .'<div class="assinatura-email">'.$assinatura->conteudo.'</div>';
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorEscopo($query, string $escopo)
    {
        return $query->where('escopo', $escopo);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('nome');
    }

    public static function escoposDisponiveis(): array
    {
        return [
            'contrato_locacao' => 'Contrato de Locação',
            'vencimento_fatura' => 'Vencimento de Fatura',
            'boas_vindas' => 'Boas-vindas',
            'renovacao_contrato' => 'Renovação de Contrato',
            'reajuste_aluguel' => 'Reajuste de Aluguel',
            'vistoria' => 'Vistoria',
            'repasse_locador' => 'Repasse ao Locador',
            'notificacao_geral' => 'Notificação Geral',
        ];
    }

    public static function variaveisPorEscopo(string $escopo): array
    {
        $todas = [
            'contrato_locacao' => [
                'nome_locatario' => 'Nome do Locatário',
                'nome_locador' => 'Nome do Locador',
                'numero_contrato' => 'Número do Contrato',
                'data_inicio' => 'Data de Início',
                'data_fim' => 'Data de Fim',
                'valor_aluguel' => 'Valor do Aluguel',
                'dia_vencimento' => 'Dia de Vencimento',
                'endereco_imovel' => 'Endereço do Imóvel',
                'valor_condominio' => 'Valor do Condomínio',
                'valor_iptu' => 'Valor do IPTU',
                'taxa_adm_percentual' => 'Taxa de Administração (%)',
            ],
            'vencimento_fatura' => [
                'nome_locatario' => 'Nome do Locatário',
                'numero_contrato' => 'Número do Contrato',
                'valor_total' => 'Valor Total da Fatura',
                'data_vencimento' => 'Data de Vencimento',
                'dias_atraso' => 'Dias de Atraso',
                'valor_multa_juros' => 'Valor de Multa/Juros',
                'link_pagamento' => 'Link para Pagamento',
            ],
            'boas_vindas' => [
                'nome_locatario' => 'Nome do Locatário',
                'nome_locador' => 'Nome do Locador',
                'numero_contrato' => 'Número do Contrato',
                'endereco_imovel' => 'Endereço do Imóvel',
                'data_inicio' => 'Data de Início',
                'contato_imobiliaria' => 'Contato da Imobiliária',
            ],
            'renovacao_contrato' => [
                'nome_locatario' => 'Nome do Locatário',
                'nome_locador' => 'Nome do Locador',
                'numero_contrato' => 'Número do Contrato',
                'data_fim_atual' => 'Data de Fim Atual',
                'nova_data_fim' => 'Nova Data de Fim',
                'novo_valor_aluguel' => 'Novo Valor do Aluguel',
            ],
            'reajuste_aluguel' => [
                'nome_locatario' => 'Nome do Locatário',
                'numero_contrato' => 'Número do Contrato',
                'valor_atual' => 'Valor Atual',
                'novo_valor' => 'Novo Valor',
                'percentual_reajuste' => 'Percentual de Reajuste',
                'data_vigencia' => 'Data de Vigência',
                'indice' => 'Índice Utilizado',
            ],
            'vistoria' => [
                'nome_locatario' => 'Nome do Locatário',
                'nome_locador' => 'Nome do Locador',
                'endereco_imovel' => 'Endereço do Imóvel',
                'data_vistoria' => 'Data da Vistoria',
                'tipo_vistoria' => 'Tipo de Vistoria (inicial/final)',
                'observacoes' => 'Observações',
            ],
            'repasse_locador' => [
                'nome_locador' => 'Nome do Locador',
                'periodo' => 'Período do Repasse',
                'valor_aluguel' => 'Valor do Aluguel',
                'valor_multa' => 'Valor da Multa',
                'valor_taxa_adm' => 'Valor da Taxa de Administração',
                'valor_custos' => 'Valor dos Custos',
                'valor_liquido' => 'Valor Líquido a Receber',
            ],
            'notificacao_geral' => [
                'nome_destinatario' => 'Nome do Destinatário',
                'titulo' => 'Título da Notificação',
                'mensagem' => 'Mensagem',
                'link' => 'Link para Ação',
            ],
        ];

        return $todas[$escopo] ?? [];
    }

    public static function gerarSlug(string $nome): string
    {
        return str()->slug($nome);
    }
}
