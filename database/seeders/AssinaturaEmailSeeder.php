<?php

namespace Database\Seeders;

use App\Models\AssinaturaEmail;
use Illuminate\Database\Seeder;

class AssinaturaEmailSeeder extends Seeder
{
    public function run(): void
    {
        AssinaturaEmail::firstOrCreate(
            ['nome' => 'Assinatura Padrão'],
            [
                'conteudo' => '<p style="margin:0;color:#495057;">Atenciosamente,<br>'
                    .'<strong>Equipe da Imobiliária</strong><br>'
                    .'Telefone: (14) 3000-0000<br>'
                    .'E-mail: contato@imobiliaria.com</p>',
                'ativo' => true,
                'sistema' => true,
            ]
        );
    }
}
