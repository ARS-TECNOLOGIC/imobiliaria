<?php

use App\Models\Configuracao;
use App\Models\ModeloEmail;
use App\Models\PastaDocumento;
use App\Services\CalculadoraIptu;
use Database\Seeders\ConfiguracaoSeeder;
use Database\Seeders\ModeloEmailSeeder;
use Database\Seeders\PastaDocumentoSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

test('helper configuracao returns default when key does not exist', function () {
    expect(configuracao('chave_inexistente', 'default_value'))->toBe('default_value');
    expect(configuracao('chave_inexistente'))->toBeNull();
});

test('helper configuracao returns seeded values', function () {
    $this->seed(ConfiguracaoSeeder::class);

    expect(configuracao('iptu_mes_inicio'))->toBe(3);
    expect(configuracao('iptu_qtd_parcelas'))->toBe(10);
    expect(configuracao('iptu_aplicar_globalmente'))->toBeTrue();
    expect(configuracao('email_remetente_nome'))->toBe('Imobiliária');
});

test('CalculadoraIptu calculates months correctly', function () {
    $this->seed(ConfiguracaoSeeder::class);

    $iptu = new CalculadoraIptu;

    expect($iptu->mesTemIptu(1))->toBeFalse();
    expect($iptu->mesTemIptu(2))->toBeFalse();
    expect($iptu->mesTemIptu(3))->toBeTrue();
    expect($iptu->mesTemIptu(12))->toBeTrue();
    expect($iptu->deveAplicarGlobalmente())->toBeTrue();
});

test('CalculadoraIptu getParcelasAno returns correct months', function () {
    $this->seed(ConfiguracaoSeeder::class);

    $iptu = new CalculadoraIptu;
    $parcelas = $iptu->getParcelasAno(2026);

    expect($parcelas)->toBe([3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);
});

test('configuracoes index page loads with tabs', function () {
    $this->seed(ConfiguracaoSeeder::class);
    $this->seed(PastaDocumentoSeeder::class);
    $this->seed(ModeloEmailSeeder::class);

    $response = $this->get(route('configuracoes.index'));

    $response->assertOk();
    $response->assertSee('Configurações do Sistema');
    $response->assertSee('Sistema');
    $response->assertSee('Documentos');
    $response->assertSee('E-mails');
    $response->assertSee('iptu_mes_inicio');
    $response->assertSee('email_remetente_nome');
});

test('can update configuration via updateMultiplo', function () {
    $this->seed(ConfiguracaoSeeder::class);

    $config = Configuracao::where('chave', 'iptu_mes_inicio')->first();

    $response = $this->put(route('configuracoes.update-multiplo'), [
        'config' => [$config->id => '5'],
        'tipos' => [$config->id => 'integer'],
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('sucesso');

    $config->refresh();
    expect($config->valor)->toBe(5);
});

test('updateMultiplo skips system configurations', function () {
    $this->seed(ConfiguracaoSeeder::class);

    $config = Configuracao::where('chave', 'email_remetente_nome')->first();
    expect($config->sistema)->toBeTrue();

    $response = $this->put(route('configuracoes.update-multiplo'), [
        'config' => [$config->id => 'Hacker'],
        'tipos' => [$config->id => 'string'],
    ]);

    $response->assertRedirect();

    $config->refresh();
    expect($config->valor)->toBe('Imobiliária');
});

test('can create new configuration via post', function () {
    $response = $this->post(route('configuracoes.store'), [
        'chave' => 'teste_nova_config',
        'valor' => '42',
        'tipo' => 'integer',
        'descricao' => 'Configuração de teste',
        'grupo' => 'geral',
        'sistema' => false,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('sucesso');

    $config = Configuracao::where('chave', 'teste_nova_config')->first();
    expect($config)->not->toBeNull();
    expect($config->valor)->toBe(42);
});

test('cannot delete system configuration', function () {
    $this->seed(ConfiguracaoSeeder::class);

    $config = Configuracao::where('chave', 'email_remetente_nome')->first();

    $response = $this->delete(route('configuracoes.destroy', $config));

    $response->assertRedirect();
    $response->assertSessionHas('erro');

    expect(Configuracao::where('chave', 'email_remetente_nome')->exists())->toBeTrue();
});

test('can delete non-system configuration', function () {
    $config = Configuracao::create([
        'chave' => 'config_teste_deletar',
        'valor' => 'teste',
        'tipo' => 'string',
        'grupo' => 'geral',
        'sistema' => false,
    ]);

    $response = $this->delete(route('configuracoes.destroy', $config));

    $response->assertRedirect();
    $response->assertSessionHas('sucesso');

    expect(Configuracao::where('chave', 'config_teste_deletar')->exists())->toBeFalse();
});

// --- PastaDocumento ---

test('documentos index page loads with folders', function () {
    $this->seed(PastaDocumentoSeeder::class);

    $response = $this->get(route('configuracoes.documentos.index'));

    $response->assertOk();
    $response->assertSee('Pastas de Documentos');
    $response->assertSee('Contratos');
    $response->assertSee('Vistorias');
});

test('can create document folder', function () {
    $response = $this->post(route('configuracoes.documentos.store'), [
        'nome' => 'Laudos Técnicos',
        'descricao' => 'Laudos e inspeções',
        'ativa' => true,
        'ordem' => 99,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('sucesso');

    $pasta = PastaDocumento::where('nome', 'Laudos Técnicos')->first();
    expect($pasta)->not->toBeNull();
    expect($pasta->slug)->toBe('laudos-tecnicos');
    expect($pasta->ativa)->toBeTrue();
    expect($pasta->sistema)->toBeFalse();
});

test('cannot delete system folder', function () {
    $pasta = PastaDocumento::create([
        'nome' => 'System Folder',
        'slug' => 'system-folder',
        'ativa' => true,
        'ordem' => 1,
        'sistema' => true,
    ]);

    $response = $this->delete(route('configuracoes.documentos.destroy', $pasta));

    $response->assertRedirect();
    $response->assertSessionHas('erro');

    expect(PastaDocumento::where('slug', 'system-folder')->exists())->toBeTrue();
});

test('can delete non-system folder', function () {
    $pasta = PastaDocumento::create([
        'nome' => 'Temp Test Folder',
        'slug' => 'temp-test-folder',
        'ativa' => true,
        'ordem' => 50,
        'sistema' => false,
    ]);

    $response = $this->delete(route('configuracoes.documentos.destroy', $pasta));

    $response->assertRedirect();
    $response->assertSessionHas('sucesso');

    expect(PastaDocumento::where('slug', 'temp-test-folder')->exists())->toBeFalse();
});

test('can update document folder', function () {
    $this->seed(PastaDocumentoSeeder::class);

    $pasta = PastaDocumento::where('slug', 'iptu')->first();
    expect($pasta->sistema)->toBeFalse();

    $response = $this->put(route('configuracoes.documentos.update', $pasta), [
        'nome' => 'IPTU e Tributos',
        'descricao' => 'Tributos municipais',
        'ativa' => true,
        'ordem' => 20,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('sucesso');

    $pasta->refresh();
    expect($pasta->nome)->toBe('IPTU e Tributos');
    expect($pasta->slug)->toBe('iptu-e-tributos');
});

// --- ModeloEmail ---

test('emails index page loads grouped by scope', function () {
    $this->seed(ModeloEmailSeeder::class);

    $response = $this->get(route('configuracoes.emails.index'));

    $response->assertOk();
    $response->assertSee('Modelos de E-mail');
    $response->assertSee('Contrato de Locação');
    $response->assertSee('Vencimento de Fatura');
    $response->assertSee('Novo Modelo');
});

test('can create email template', function () {
    $response = $this->post(route('configuracoes.emails.store'), [
        'nome' => 'Lembrete Personalizado',
        'escopo' => 'notificacao_geral',
        'remetente_email' => 'teste@imobiliaria.com',
        'remetente_nome' => 'Teste',
        'assunto' => 'Lembrete: {{titulo}}',
        'corpo' => '<p>Olá {{nome_destinatario}},</p><p>{{mensagem}}</p>',
        'ativo' => true,
        'descricao' => 'Modelo de teste',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('sucesso');

    $modelo = ModeloEmail::where('nome', 'Lembrete Personalizado')->first();
    expect($modelo)->not->toBeNull();
    expect($modelo->slug)->toBe('lembrete-personalizado');
    expect($modelo->escopo)->toBe('notificacao_geral');
    expect($modelo->ativo)->toBeTrue();
});

test('email template validation requires escopo in list', function () {
    $response = $this->post(route('configuracoes.emails.store'), [
        'nome' => 'Escopo Inválido',
        'escopo' => 'escopo_inexistente',
        'remetente_email' => 'teste@imobiliaria.com',
        'remetente_nome' => 'Teste',
        'assunto' => 'Assunto',
        'corpo' => '<p>Corpo</p>',
        'ativo' => true,
    ]);

    $response->assertSessionHasErrors('escopo');
});

test('can view email template', function () {
    $this->seed(ModeloEmailSeeder::class);

    $modelo = ModeloEmail::where('slug', 'boas-vindas-locatario')->first();

    $response = $this->get(route('configuracoes.emails.show', $modelo));

    $response->assertOk();
    $response->assertSee('Boas-vindas ao Locatário');
    $response->assertSee('nome_locatario');
});

test('can edit email template', function () {
    $this->seed(ModeloEmailSeeder::class);

    $modelo = ModeloEmail::where('slug', 'notificacao-geral-padrao')->first();
    expect($modelo->sistema)->toBeTrue();

    $response = $this->get(route('configuracoes.emails.edit', $modelo));

    $response->assertOk();
    $response->assertSee('Editar Modelo de E-mail');
});

test('can update email template', function () {
    $this->seed(ModeloEmailSeeder::class);

    $modelo = ModeloEmail::where('slug', 'notificacao-geral-padrao')->first();

    $response = $this->put(route('configuracoes.emails.update', $modelo), [
        'nome' => 'Notificação Geral - Atualizado',
        'escopo' => 'notificacao_geral',
        'remetente_email' => 'atualizado@imobiliaria.com',
        'remetente_nome' => 'Imobiliária',
        'assunto' => 'Novo título: {{titulo}}',
        'corpo' => '<p>Corpo atualizado</p>',
        'ativo' => true,
        'descricao' => 'Atualizado',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('sucesso');

    $modelo->refresh();
    expect($modelo->nome)->toBe('Notificação Geral - Atualizado');
    expect($modelo->remetente_email)->toBe('atualizado@imobiliaria.com');
});

test('cannot delete system email template', function () {
    $this->seed(ModeloEmailSeeder::class);

    $modelo = ModeloEmail::where('slug', 'boas-vindas-locatario')->first();

    $response = $this->delete(route('configuracoes.emails.destroy', $modelo));

    $response->assertRedirect();
    $response->assertSessionHas('erro');

    expect(ModeloEmail::where('slug', 'boas-vindas-locatario')->exists())->toBeTrue();
});

test('variaveis endpoint returns variables for scope', function () {
    $response = $this->get(route('configuracoes.emails.variaveis').'?escopo=contrato_locacao');

    $response->assertOk();
    $response->assertJsonFragment(['nome_locatario' => 'Nome do Locatário']);
    $response->assertJsonFragment(['numero_contrato' => 'Número do Contrato']);
});

test('variaveis endpoint returns empty for unknown scope', function () {
    $response = $this->get(route('configuracoes.emails.variaveis').'?escopo=inexistente');

    $response->assertOk();
    $response->assertExactJson([]);
});

test('email corpo is sanitized on save', function () {
    $response = $this->post(route('configuracoes.emails.store'), [
        'nome' => 'Modelo com XSS',
        'escopo' => 'notificacao_geral',
        'remetente_email' => 'teste@imobiliaria.com',
        'remetente_nome' => 'Teste',
        'assunto' => 'Assunto seguro',
        'corpo' => '<p>Olá {{nome_destinatario}}</p><script>alert(1)</script><p onclick="evil()">Clique</p><a href="javascript:evil()">link</a>',
        'ativo' => true,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('sucesso');

    $modelo = ModeloEmail::where('nome', 'Modelo com XSS')->first();
    expect($modelo->corpo)->toContain('<p>Olá {{nome_destinatario}}</p>');
    expect($modelo->corpo)->not->toContain('<script');
    expect($modelo->corpo)->not->toContain('onclick');
    expect($modelo->corpo)->not->toContain('javascript:');
});

test('email corpo keeps template variables and allowed formatting', function () {
    $response = $this->post(route('configuracoes.emails.store'), [
        'nome' => 'Modelo Formatado',
        'escopo' => 'boas_vindas',
        'remetente_email' => 'teste@imobiliaria.com',
        'remetente_nome' => 'Teste',
        'assunto' => 'Bem-vindo {{nome_locatario}}',
        'corpo' => '<h2>Olá</h2><p><strong>{{nome_locatario}}</strong></p><ul><li>Item</li></ul>',
        'ativo' => true,
    ]);

    $response->assertSessionHas('sucesso');

    $modelo = ModeloEmail::where('nome', 'Modelo Formatado')->first();
    expect($modelo->corpo)->toContain('<h2>Olá</h2>');
    expect($modelo->corpo)->toContain('<strong>{{nome_locatario}}</strong>');
    expect($modelo->corpo)->toContain('<ul><li>Item</li></ul>');
});

test('email corpo allows img tags from editor upload', function () {
    $response = $this->post(route('configuracoes.emails.store'), [
        'nome' => 'Modelo com Imagem',
        'escopo' => 'notificacao_geral',
        'remetente_email' => 'teste@imobiliaria.com',
        'remetente_nome' => 'Teste',
        'assunto' => 'Assunto',
        'corpo' => '<p>Olá</p><img src="http://localhost/storage/emails/imagens/logo.png" alt="" style="max-width:100%;">',
        'ativo' => true,
    ]);

    $response->assertSessionHas('sucesso');

    $modelo = ModeloEmail::where('nome', 'Modelo com Imagem')->first();
    expect($modelo->corpo)->toContain('<img src=');
});

test('email corpo keeps img width style for resizing', function () {
    $response = $this->post(route('configuracoes.emails.store'), [
        'nome' => 'Modelo Imagem Redimensionada',
        'escopo' => 'notificacao_geral',
        'remetente_email' => 'teste@imobiliaria.com',
        'remetente_nome' => 'Teste',
        'assunto' => 'Assunto',
        'corpo' => '<p>Olá</p><img src="/storage/emails/imagens/logo.png" alt="" style="width:50%;max-width:100%;height:auto;">',
        'ativo' => true,
    ]);

    $response->assertSessionHas('sucesso');

    $modelo = ModeloEmail::where('nome', 'Modelo Imagem Redimensionada')->first();
    expect($modelo->corpo)->toContain('width:50%');
});

test('email corpo strips data uris regardless of quote style', function () {
    $response = $this->post(route('configuracoes.emails.store'), [
        'nome' => 'Modelo Data URI',
        'escopo' => 'notificacao_geral',
        'remetente_email' => 'teste@imobiliaria.com',
        'remetente_nome' => 'Teste',
        'assunto' => 'Assunto',
        'corpo' => '<p>Olá</p>'
            .'<img src="data:image/png;base64,AAAA">'
            ."<img src='data:image/png;base64,BBBB'>"
            .'<img src=data:image/png;base64,CCCC>',
        'ativo' => true,
    ]);

    $response->assertSessionHas('sucesso');

    $modelo = ModeloEmail::where('nome', 'Modelo Data URI')->first();
    expect($modelo->corpo)->not->toContain('data:image');
});

test('can upload image for email body', function () {
    Storage::fake('public');

    $response = $this->post(route('configuracoes.emails.imagens.store'), [
        'imagem' => UploadedFile::fake()->image('logo.png'),
    ]);

    $response->assertOk();
    $response->assertJson(['ok' => true]);
    expect($response->json('url'))->toStartWith('/storage/emails/imagens/');
    expect(Storage::disk('public')->files('emails/imagens'))->toHaveCount(1);
});

test('can attach file to email template', function () {
    Storage::fake('privada');
    $this->seed(ModeloEmailSeeder::class);

    $modelo = ModeloEmail::where('slug', 'boas-vindas-locatario')->first();

    $response = $this->post(route('configuracoes.emails.anexos.store', $modelo), [
        'arquivo' => UploadedFile::fake()->create('contrato.pdf', 100, 'application/pdf'),
    ]);

    $response->assertRedirect(route('configuracoes.emails.edit', $modelo));
    $response->assertSessionHas('sucesso');

    expect($modelo->anexos()->count())->toBe(1);
    $anexo = $modelo->anexos()->first();
    Storage::disk('privada')->assertExists($anexo->caminho_arquivo);
});

test('can delete email template attachment', function () {
    Storage::fake('privada');
    $this->seed(ModeloEmailSeeder::class);

    $modelo = ModeloEmail::where('slug', 'boas-vindas-locatario')->first();

    $this->post(route('configuracoes.emails.anexos.store', $modelo), [
        'arquivo' => UploadedFile::fake()->create('anexo.pdf', 10, 'application/pdf'),
    ]);

    $anexo = $modelo->anexos()->first();
    expect($anexo)->not->toBeNull();

    $response = $this->delete(route('configuracoes.emails.anexos.destroy', [$modelo, $anexo]));

    $response->assertRedirect(route('configuracoes.emails.edit', $modelo));
    expect($modelo->anexos()->count())->toBe(0);
    Storage::disk('privada')->assertMissing($anexo->caminho_arquivo);
});

test('deleting custom email template removes anexo files from storage', function () {
    Storage::fake('privada');

    $this->post(route('configuracoes.emails.store'), [
        'nome' => 'Modelo Com Anexo',
        'escopo' => 'notificacao_geral',
        'remetente_email' => 'teste@imobiliaria.com',
        'remetente_nome' => 'Teste',
        'assunto' => 'Assunto',
        'corpo' => '<p>Olá</p>',
        'ativo' => true,
    ]);

    $modelo = ModeloEmail::where('nome', 'Modelo Com Anexo')->firstOrFail();

    $this->post(route('configuracoes.emails.anexos.store', $modelo), [
        'arquivo' => UploadedFile::fake()->create('documento.pdf', 10, 'application/pdf'),
    ]);

    $anexo = $modelo->anexos()->firstOrFail();
    Storage::disk('privada')->assertExists($anexo->caminho_arquivo);

    $this->delete(route('configuracoes.emails.destroy', $modelo));

    Storage::disk('privada')->assertMissing($anexo->caminho_arquivo);
    expect(ModeloEmail::where('id', $modelo->id)->exists())->toBeFalse();
});

test('deleting custom email template removes unreferenced body images', function () {
    Storage::fake('public');

    Storage::disk('public')->put('emails/imagens/exclusiva.png', 'png');
    $url = Storage::disk('public')->url('emails/imagens/exclusiva.png');

    $this->post(route('configuracoes.emails.store'), [
        'nome' => 'Modelo Imagem Exclusiva',
        'escopo' => 'notificacao_geral',
        'remetente_email' => 'teste@imobiliaria.com',
        'remetente_nome' => 'Teste',
        'assunto' => 'Assunto',
        'corpo' => '<p><img src="'.$url.'"></p>',
        'ativo' => true,
    ]);

    $modelo = ModeloEmail::where('nome', 'Modelo Imagem Exclusiva')->firstOrFail();
    Storage::disk('public')->assertExists('emails/imagens/exclusiva.png');

    $this->delete(route('configuracoes.emails.destroy', $modelo));

    Storage::disk('public')->assertMissing('emails/imagens/exclusiva.png');
});

test('shared body image survives when another template still references it', function () {
    Storage::fake('public');

    Storage::disk('public')->put('emails/imagens/compartilhada.png', 'png');
    $url = Storage::disk('public')->url('emails/imagens/compartilhada.png');

    foreach (['Modelo Compartilhado A', 'Modelo Compartilhado B'] as $nome) {
        $this->post(route('configuracoes.emails.store'), [
            'nome' => $nome,
            'escopo' => 'notificacao_geral',
            'remetente_email' => 'teste@imobiliaria.com',
            'remetente_nome' => 'Teste',
            'assunto' => 'Assunto',
            'corpo' => '<p><img src="'.$url.'"></p>',
            'ativo' => true,
        ]);
    }

    $modeloA = ModeloEmail::where('nome', 'Modelo Compartilhado A')->firstOrFail();

    $this->delete(route('configuracoes.emails.destroy', $modeloA));

    Storage::disk('public')->assertExists('emails/imagens/compartilhada.png');
    expect(ModeloEmail::where('nome', 'Modelo Compartilhado B')->exists())->toBeTrue();
});

test('updating email template removes replaced body images only', function () {
    Storage::fake('public');

    Storage::disk('public')->put('emails/imagens/antiga.png', 'png');
    Storage::disk('public')->put('emails/imagens/manutencao.png', 'png');
    $urlAntiga = Storage::disk('public')->url('emails/imagens/antiga.png');
    $urlManutencao = Storage::disk('public')->url('emails/imagens/manutencao.png');

    $this->post(route('configuracoes.emails.store'), [
        'nome' => 'Modelo Troca Imagem',
        'escopo' => 'notificacao_geral',
        'remetente_email' => 'teste@imobiliaria.com',
        'remetente_nome' => 'Teste',
        'assunto' => 'Assunto',
        'corpo' => '<p><img src="'.$urlAntiga.'"></p><p><img src="'.$urlManutencao.'"></p>',
        'ativo' => true,
    ]);

    $modelo = ModeloEmail::where('nome', 'Modelo Troca Imagem')->firstOrFail();

    $this->put(route('configuracoes.emails.update', $modelo), [
        'nome' => 'Modelo Troca Imagem',
        'escopo' => 'notificacao_geral',
        'remetente_email' => 'teste@imobiliaria.com',
        'remetente_nome' => 'Teste',
        'assunto' => 'Assunto',
        'corpo' => '<p><img src="'.$urlManutencao.'"></p>',
        'ativo' => true,
    ])->assertSessionHas('sucesso');

    Storage::disk('public')->assertMissing('emails/imagens/antiga.png');
    Storage::disk('public')->assertExists('emails/imagens/manutencao.png');
});

test('edit page keeps anexo forms outside the update form', function () {
    $this->seed(ModeloEmailSeeder::class);

    $modelo = ModeloEmail::where('slug', 'boas-vindas-locatario')->first();

    $response = $this->get(route('configuracoes.emails.edit', $modelo));

    $response->assertOk();
    $html = $response->getContent();
    $posUpdateForm = strpos($html, 'route(');
    $posFechamentoUpdate = strpos($html, '</form>');
    $posFormAnexo = strpos($html, route('configuracoes.emails.anexos.store', $modelo));

    expect($posFechamentoUpdate)->not->toBeFalse();
    expect($posFormAnexo)->not->toBeFalse();
    expect($posFormAnexo)->toBeGreaterThan($posFechamentoUpdate);
});

test('assunto field supports template variables', function () {
    $response = $this->post(route('configuracoes.emails.store'), [
        'nome' => 'Modelo Assunto Variavel',
        'escopo' => 'vencimento_fatura',
        'remetente_email' => 'teste@imobiliaria.com',
        'remetente_nome' => 'Teste',
        'assunto' => 'Fatura {{numero_contrato}} vence em {{data_vencimento}}',
        'corpo' => '<p>Olá</p>',
        'ativo' => true,
    ]);

    $response->assertSessionHas('sucesso');

    $modelo = ModeloEmail::where('nome', 'Modelo Assunto Variavel')->first();
    expect($modelo->assunto)->toBe('Fatura {{numero_contrato}} vence em {{data_vencimento}}');
});
