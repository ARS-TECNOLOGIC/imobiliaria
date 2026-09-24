<?php

namespace Database\Seeders;

use App\Models\Configuracao;
use Illuminate\Database\Seeder;

class ConfiguracaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            // IPTU
            [
                'chave' => 'iptu_mes_inicio',
                'valor' => 3,
                'tipo' => 'integer',
                'descricao' => 'Mês inicial do IPTU (1-12)',
                'grupo' => 'iptu',
                'sistema' => false,
            ],
            [
                'chave' => 'iptu_qtd_parcelas',
                'valor' => 10,
                'tipo' => 'integer',
                'descricao' => 'Quantidade de parcelas anuais do IPTU',
                'grupo' => 'iptu',
                'sistema' => false,
            ],
            [
                'chave' => 'iptu_aplicar_globalmente',
                'valor' => true,
                'tipo' => 'boolean',
                'descricao' => 'Usar configuração global de IPTU em vez do contrato',
                'grupo' => 'iptu',
                'sistema' => false,
            ],

            // E-mail
            [
                'chave' => 'email_remetente_nome',
                'valor' => 'Imobiliária',
                'tipo' => 'string',
                'descricao' => 'Nome do remetente dos e-mails',
                'grupo' => 'email',
                'sistema' => true,
            ],
            [
                'chave' => 'email_remetente_endereco',
                'valor' => 'naoresponda@localhost',
                'tipo' => 'string',
                'descricao' => 'E-mail do remetente',
                'grupo' => 'email',
                'sistema' => true,
            ],
            [
                'chave' => 'email_assinatura_padrao',
                'valor' => '<p>Atenciosamente,<br>Equipe</p>',
                'tipo' => 'string',
                'descricao' => 'Assinatura padrão dos e-mails (HTML)',
                'grupo' => 'email',
                'sistema' => true,
            ],
        ];

        foreach ($defaults as $config) {
            Configuracao::firstOrCreate(
                ['chave' => $config['chave']],
                $config
            );
        }
    }
}
