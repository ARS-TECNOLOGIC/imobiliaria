<?php

namespace Database\Seeders;

use App\Models\ModeloEmail;
use Illuminate\Database\Seeder;

class ModeloEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modelos = [
            // Contrato de Locação
            [
                'nome' => 'Contrato de Locação - Assinatura',
                'slug' => 'contrato-locacao-assinatura',
                'escopo' => 'contrato_locacao',
                'remetente_email' => 'contratos@imobiliaria.com',
                'remetente_nome' => 'Imobiliária - Contratos',
                'assunto' => 'Contrato de Locação Nº {{numero_contrato}} - Assinatura Digital',
                'corpo' => '<p>Olá {{nome_locatario}},</p>
<p>Seu contrato de locação <strong>Nº {{numero_contrato}}</strong> está pronto para assinatura digital.</p>
<p><strong>Detalhes do contrato:</strong></p>
<ul>
    <li>Imóvel: {{endereco_imovel}}</li>
    <li>Período: {{data_inicio}} a {{data_fim}}</li>
    <li>Aluguel: R$ {{valor_aluguel}}</li>
    <li>Vencimento: Dia {{dia_vencimento}}</li>
    <li>Condomínio: R$ {{valor_condominio}}</li>
    <li>IPTU: R$ {{valor_iptu}}</li>
</ul>
<p>Acesse o link abaixo para assinar:</p>
<p><a href="{{link_assinatura}}" style="background:#007bff;color:#fff;padding:10px 20px;text-decoration:none;border-radius:4px;display:inline-block;">Assinar Contrato</a></p>
<p>Atenciosamente,<br>Equipe de Contratos</p>',
                'ativo' => true,
                'sistema' => true,
                'descricao' => 'Enviado quando o contrato é gerado e precisa ser assinado',
            ],
            [
                'nome' => 'Contrato de Locação - Vencimento Próximo',
                'slug' => 'contrato-locacao-vencimento-proximo',
                'escopo' => 'contrato_locacao',
                'remetente_email' => 'contratos@imobiliaria.com',
                'remetente_nome' => 'Imobiliária - Contratos',
                'assunto' => 'Seu contrato Nº {{numero_contrato}} vence em 30 dias',
                'corpo' => '<p>Olá {{nome_locatario}},</p>
<p>Informamos que seu contrato de locação <strong>Nº {{numero_contrato}}</strong> terá seu prazo finalizado em <strong>{{data_fim}}</strong> (daqui a 30 dias).</p>
<p>Caso tenha interesse na renovação, entre em contato conosco com antecedência para alinharmos as condições.</p>
<p>Atenciosamente,<br>Equipe de Contratos</p>',
                'ativo' => true,
                'sistema' => true,
                'descricao' => 'Enviado 30 dias antes do fim do contrato',
            ],

            // Vencimento de Fatura
            [
                'nome' => 'Fatura - Vencimento Hoje',
                'slug' => 'fatura-vencimento-hoje',
                'escopo' => 'vencimento_fatura',
                'remetente_email' => 'financeiro@imobiliaria.com',
                'remetente_nome' => 'Imobiliária - Financeiro',
                'assunto' => 'Fatura Nº {{numero_fatura}} vence hoje - Contrato {{numero_contrato}}',
                'corpo' => '<p>Olá {{nome_locatario}},</p>
<p>Lembramos que a fatura referente ao contrato <strong>Nº {{numero_contrato}}</strong> vence <strong>hoje ({{data_vencimento}})</strong>.</p>
<p><strong>Valor total: R$ {{valor_total}}</strong></p>
<p>Para evitar multas e juros, realize o pagamento até o final do dia.</p>
<p><a href="{{link_pagamento}}" style="background:#28a745;color:#fff;padding:10px 20px;text-decoration:none;border-radius:4px;display:inline-block;">Pagar Agora</a></p>
<p>Atenciosamente,<br>Setor Financeiro</p>',
                'ativo' => true,
                'sistema' => true,
                'descricao' => 'Enviado no dia do vencimento da fatura',
            ],
            [
                'nome' => 'Fatura - Atraso (1º Aviso)',
                'slug' => 'fatura-atraso-primeiro-aviso',
                'escopo' => 'vencimento_fatura',
                'remetente_email' => 'financeiro@imobiliaria.com',
                'remetente_nome' => 'Imobiliária - Financeiro',
                'assunto' => 'Atraso no pagamento - Fatura Nº {{numero_fatura}} - Contrato {{numero_contrato}}',
                'corpo' => '<p>Olá {{nome_locatario}},</p>
<p>Identificamos que a fatura <strong>Nº {{numero_fatura}}</strong> do contrato <strong>Nº {{numero_contrato}}</strong> está em atraso há <strong>{{dias_atraso}} dia(s)</strong>.</p>
<p><strong>Valor original: R$ {{valor_total}}</strong><br>
<strong>Multa/Juros: R$ {{valor_multa_juros}}</strong><br>
<strong>Total a pagar: R$ {{valor_com_multa}}</strong></p>
<p>Regularize sua situação o quanto antes para evitar medidas adicionais.</p>
<p><a href="{{link_pagamento}}" style="background:#dc3545;color:#fff;padding:10px 20px;text-decoration:none;border-radius:4px;display:inline-block;">Regularizar Pagamento</a></p>
<p>Atenciosamente,<br>Setor Financeiro</p>',
                'ativo' => true,
                'sistema' => true,
                'descricao' => 'Primeiro aviso de atraso (após 5 dias úteis)',
            ],

            // Boas-vindas
            [
                'nome' => 'Boas-vindas ao Locatário',
                'slug' => 'boas-vindas-locatario',
                'escopo' => 'boas_vindas',
                'remetente_email' => 'atendimento@imobiliaria.com',
                'remetente_nome' => 'Imobiliária - Atendimento',
                'assunto' => 'Bem-vindo à {{nome_imobiliaria}}, {{nome_locatario}}!',
                'corpo' => '<p>Olá {{nome_locatario}},</p>
<p>Seja muito bem-vindo! É um prazer tê-lo como nosso cliente.</p>
<p>Seu contrato <strong>Nº {{numero_contrato}}</strong> para o imóvel <strong>{{endereco_imovel}}</strong> inicia em <strong>{{data_inicio}}</strong>.</p>
<p><strong>Informações importantes:</strong></p>
<ul>
    <li>Vencimento do aluguel: Dia {{dia_vencimento}} de cada mês</li>
    <li>Valor do aluguel: R$ {{valor_aluguel}}</li>
    <li>Condomínio: R$ {{valor_condominio}}</li>
    <li>Contato da imobiliária: {{contato_imobiliaria}}</li>
</ul>
<p>Estamos à disposição para qualquer dúvida.</p>
<p>Atenciosamente,<br>Equipe de Atendimento</p>',
                'ativo' => true,
                'sistema' => true,
                'descricao' => 'Enviado quando o locatário assina o contrato',
            ],

            // Repasse ao Locador
            [
                'nome' => 'Repasse ao Locador - Comprovante',
                'slug' => 'repasse-locador-comprovante',
                'escopo' => 'repasse_locador',
                'remetente_email' => 'repasse@imobiliaria.com',
                'remetente_nome' => 'Imobiliária - Repasses',
                'assunto' => 'Repasse {{periodo}} - Contrato {{numero_contrato}} - R$ {{valor_liquido}}',
                'corpo' => '<p>Olá {{nome_locador}},</p>
<p>Segue o comprovante de repasse referente ao período <strong>{{periodo}}</strong>.</p>
<p><strong>Detalhes do repasse (Contrato Nº {{numero_contrato}}):</strong></p>
<ul>
    <li>Aluguel recebido: R$ {{valor_aluguel}}</li>
    <li>Multa recebida: R$ {{valor_multa}}</li>
    <li>Taxa de administração ({{taxa_adm_percentual}}%): R$ {{valor_taxa_adm}}</li>
    <li>Custos operacionais: R$ {{valor_custos}}</li>
    <li><strong>Valor líquido depositado: R$ {{valor_liquido}}</strong></li>
</ul>
<p>O valor foi depositado na sua conta cadastrada.</p>
<p>Atenciosamente,<br>Setor de Repasses</p>',
                'ativo' => true,
                'sistema' => true,
                'descricao' => 'Comprovante de repasse mensal ao locador',
            ],

            // Notificação Geral
            [
                'nome' => 'Notificação Geral - Padrão',
                'slug' => 'notificacao-geral-padrao',
                'escopo' => 'notificacao_geral',
                'remetente_email' => 'naoresponda@imobiliaria.com',
                'remetente_nome' => 'Imobiliária',
                'assunto' => '{{titulo}}',
                'corpo' => '<p>Olá {{nome_destinatario}},</p>
<p>{{mensagem}}</p>
<p><a href="{{link}}" style="background:#007bff;color:#fff;padding:10px 20px;text-decoration:none;border-radius:4px;display:inline-block;">Acessar</a></p>
<p>Atenciosamente,<br>Equipe</p>',
                'ativo' => true,
                'sistema' => true,
                'descricao' => 'Modelo genérico para notificações diversas',
            ],
        ];

        foreach ($modelos as $modelo) {
            ModeloEmail::firstOrCreate(
                ['slug' => $modelo['slug']],
                $modelo
            );
        }
    }
}
