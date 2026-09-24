<?php

namespace Database\Seeders;

use App\Models\PastaDocumento;
use Illuminate\Database\Seeder;

class PastaDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pastas = [
            [
                'nome' => 'Contratos',
                'slug' => 'contratos',
                'descricao' => 'Contratos de locação e aditivos',
                'ativa' => true,
                'ordem' => 1,
                'sistema' => false,
            ],
            [
                'nome' => 'Documentos do Locador',
                'slug' => 'documentos-locador',
                'descricao' => 'Documentos pessoais e comprovantes do locador',
                'ativa' => true,
                'ordem' => 2,
                'sistema' => false,
            ],
            [
                'nome' => 'Documentos do Locatário',
                'slug' => 'documentos-locatario',
                'descricao' => 'Documentos pessoais, comprovantes de renda e fiador do locatário',
                'ativa' => true,
                'ordem' => 3,
                'sistema' => false,
            ],
            [
                'nome' => 'Imóveis',
                'slug' => 'imoveis',
                'descricao' => 'Documentação dos imóveis (matrícula, IPTU, fotos, laudos)',
                'ativa' => true,
                'ordem' => 4,
                'sistema' => false,
            ],
            [
                'nome' => 'Vistorias',
                'slug' => 'vistorias',
                'descricao' => 'Laudos de vistoria inicial, periódica e final',
                'ativa' => true,
                'ordem' => 5,
                'sistema' => false,
            ],
            [
                'nome' => 'Financeiro',
                'slug' => 'financeiro',
                'descricao' => 'Comprovantes de pagamento, boletos, repasses, notas fiscais',
                'ativa' => true,
                'ordem' => 6,
                'sistema' => false,
            ],
            [
                'nome' => 'Seguros e Fianças',
                'slug' => 'seguros-fiancas',
                'descricao' => 'Apólices de seguro fiança, cartas de fiança, seguros incêndio',
                'ativa' => true,
                'ordem' => 7,
                'sistema' => false,
            ],
            [
                'nome' => 'Correspondências',
                'slug' => 'correspondencias',
                'descricao' => 'Notificações, cartas, e-mails e comunicados formais',
                'ativa' => true,
                'ordem' => 8,
                'sistema' => false,
            ],
            [
                'nome' => 'IPTU',
                'slug' => 'iptu',
                'descricao' => 'Carnês e comprovantes de IPTU',
                'ativa' => true,
                'ordem' => 9,
                'sistema' => false,
            ],
            [
                'nome' => 'Certidões',
                'slug' => 'certidoes',
                'descricao' => 'Certidões negativas, certidões de ônus reais',
                'ativa' => true,
                'ordem' => 10,
                'sistema' => false,
            ],
            [
                'nome' => 'Alvarás',
                'slug' => 'alvaras',
                'descricao' => 'Alvarás de funcionamento, construção, reforma',
                'ativa' => true,
                'ordem' => 11,
                'sistema' => false,
            ],
            [
                'nome' => 'Condomínio',
                'slug' => 'condominio',
                'descricao' => 'Convenção, regimento interno, atas, balancetes',
                'ativa' => true,
                'ordem' => 12,
                'sistema' => false,
            ],
        ];

        foreach ($pastas as $pasta) {
            PastaDocumento::firstOrCreate(
                ['slug' => $pasta['slug']],
                $pasta
            );
        }
    }
}
