<?php

use App\Models\AssinaturaEmail;
use App\Models\ModeloEmail;
use Database\Seeders\ModeloEmailSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

function criarAssinatura(array $dados = []): AssinaturaEmail
{
    return AssinaturaEmail::create(array_merge([
        'nome' => 'Assinatura Teste',
        'conteudo' => '<p>Atenciosamente,<br>João Corretor</p>',
        'ativo' => true,
        'sistema' => false,
    ], $dados));
}

test('can create assinatura', function () {
    $response = $this->post(route('configuracoes.assinaturas.store'), [
        'nome' => 'Assinatura Comercial',
        'conteudo' => '<p>Equipe Comercial</p>',
        'ativo' => true,
    ]);

    $response->assertRedirect(route('configuracoes.index'));
    $response->assertSessionHas('sucesso');
    $response->assertSessionHas('aba', 'emails');

    expect(AssinaturaEmail::where('nome', 'Assinatura Comercial')->exists())->toBeTrue();
});

test('assinatura validation requires nome and conteudo', function () {
    $response = $this->post(route('configuracoes.assinaturas.store'), [
        'nome' => '',
        'conteudo' => '',
    ]);

    $response->assertSessionHasErrors(['nome', 'conteudo']);
});

test('assinatura nome must be unique', function () {
    criarAssinatura(['nome' => 'Duplicada']);

    $response = $this->post(route('configuracoes.assinaturas.store'), [
        'nome' => 'Duplicada',
        'conteudo' => '<p>Outra</p>',
    ]);

    $response->assertSessionHasErrors('nome');
});

test('can update assinatura', function () {
    $assinatura = criarAssinatura();

    $response = $this->put(route('configuracoes.assinaturas.update', $assinatura), [
        'nome' => 'Assinatura Atualizada',
        'conteudo' => '<p>Novo conteúdo</p>',
        'ativo' => 1,
    ]);

    $response->assertRedirect(route('configuracoes.index'));
    $response->assertSessionHas('sucesso');

    $assinatura->refresh();
    expect($assinatura->nome)->toBe('Assinatura Atualizada');
    expect($assinatura->conteudo)->toContain('Novo conteúdo');
    expect($assinatura->ativo)->toBeTrue();
});

test('can delete assinatura', function () {
    $assinatura = criarAssinatura();

    $response = $this->delete(route('configuracoes.assinaturas.destroy', $assinatura));

    $response->assertRedirect(route('configuracoes.index'));
    $response->assertSessionHas('sucesso');

    expect(AssinaturaEmail::where('id', $assinatura->id)->exists())->toBeFalse();
});

test('cannot delete system assinatura', function () {
    $assinatura = criarAssinatura(['nome' => 'Sistema', 'sistema' => true, 'ativo' => true]);

    $response = $this->delete(route('configuracoes.assinaturas.destroy', $assinatura));

    $response->assertSessionHas('erro');
    expect(AssinaturaEmail::where('id', $assinatura->id)->exists())->toBeTrue();
});

test('system assinatura stays active on update', function () {
    $assinatura = criarAssinatura(['nome' => 'Sistema', 'sistema' => true, 'ativo' => true]);

    $this->put(route('configuracoes.assinaturas.update', $assinatura), [
        'nome' => 'Sistema Editada',
        'conteudo' => '<p>Editada</p>',
    ])->assertSessionHas('sucesso');

    $assinatura->refresh();
    expect($assinatura->sistema)->toBeTrue();
    expect($assinatura->ativo)->toBeTrue();
});

test('assinatura sanitizes script tags and event handlers', function () {
    $response = $this->post(route('configuracoes.assinaturas.store'), [
        'nome' => 'Maliciosa',
        'conteudo' => '<p>Olá</p><script>alert(1)</script><a href="#" onclick="alert(2)">link</a>',
        'ativo' => true,
    ]);

    $response->assertSessionHas('sucesso');

    $assinatura = AssinaturaEmail::where('nome', 'Maliciosa')->first();
    expect($assinatura->conteudo)->not->toContain('<script');
    expect($assinatura->conteudo)->not->toContain('onclick');
    expect($assinatura->conteudo)->toContain('<p>Olá</p>');
});

test('assinatura keeps img tags on save', function () {
    $response = $this->post(route('configuracoes.assinaturas.store'), [
        'nome' => 'Com Imagem',
        'conteudo' => '<p>Olá</p><img src="/storage/emails/imagens/logo.png" alt="" style="width:50%;max-width:100%;height:auto;">',
        'ativo' => true,
    ]);

    $response->assertSessionHas('sucesso');

    $assinatura = AssinaturaEmail::where('nome', 'Com Imagem')->first();
    expect($assinatura->conteudo)->toContain('<img');
    expect($assinatura->conteudo)->toContain('/storage/emails/imagens/logo.png');
    expect($assinatura->conteudo)->toContain('width:50%');
});

test('assinatura update keeps img tags', function () {
    $assinatura = criarAssinatura();

    $this->put(route('configuracoes.assinaturas.update', $assinatura), [
        'nome' => 'Assinatura Teste',
        'conteudo' => '<p>Texto</p><img src="/storage/emails/imagens/foto.png" style="width:100%;">',
        'ativo' => 1,
    ])->assertSessionHas('sucesso');

    $assinatura->refresh();
    expect($assinatura->conteudo)->toContain('<img');
    expect($assinatura->conteudo)->toContain('foto.png');
});

test('assinatura strips data uri images', function () {
    $response = $this->post(route('configuracoes.assinaturas.store'), [
        'nome' => 'Data URI',
        'conteudo' => '<p>Olá</p><img src="data:image/png;base64,AAAA">',
        'ativo' => true,
    ]);

    $response->assertSessionHas('sucesso');

    $assinatura = AssinaturaEmail::where('nome', 'Data URI')->first();
    expect($assinatura->conteudo)->not->toContain('data:image');
});

test('model can persist assinatura_email_id', function () {
    $assinatura = criarAssinatura();

    $response = $this->post(route('configuracoes.emails.store'), [
        'nome' => 'Modelo com Assinatura',
        'escopo' => 'notificacao_geral',
        'remetente_email' => 'teste@imobiliaria.com',
        'remetente_nome' => 'Teste',
        'assunto' => 'Assunto',
        'corpo' => '<p>Corpo</p>',
        'ativo' => true,
        'assinatura_email_id' => $assinatura->id,
    ]);

    $response->assertSessionHas('sucesso');

    $modelo = ModeloEmail::where('nome', 'Modelo com Assinatura')->first();
    expect($modelo->assinatura_email_id)->toBe($assinatura->id);
    expect($modelo->assinatura->nome)->toBe('Assinatura Teste');
});

test('model assinatura validation rejects non existing id', function () {
    $response = $this->post(route('configuracoes.emails.store'), [
        'nome' => 'Modelo FK Inválida',
        'escopo' => 'notificacao_geral',
        'remetente_email' => 'teste@imobiliaria.com',
        'remetente_nome' => 'Teste',
        'assunto' => 'Assunto',
        'corpo' => '<p>Corpo</p>',
        'ativo' => true,
        'assinatura_email_id' => 99999,
    ]);

    $response->assertSessionHasErrors('assinatura_email_id');
});

test('corpoComAssinatura appends active signature', function () {
    $assinatura = criarAssinatura();
    $modelo = ModeloEmail::create([
        'nome' => 'Modelo Preview',
        'slug' => 'modelo-preview',
        'escopo' => 'notificacao_geral',
        'remetente_email' => 'teste@imobiliaria.com',
        'remetente_nome' => 'Teste',
        'assunto' => 'Assunto',
        'corpo' => '<p>Corpo do e-mail</p>',
        'ativo' => true,
        'sistema' => false,
        'assinatura_email_id' => $assinatura->id,
    ]);

    $html = $modelo->corpoComAssinatura();

    expect($html)->toContain('<p>Corpo do e-mail</p>');
    expect($html)->toContain('João Corretor');
    expect($html)->toContain('assinatura-email');
});

test('corpoComAssinatura returns plain corpo without signature', function () {
    $modelo = ModeloEmail::create([
        'nome' => 'Modelo Sem Assinatura',
        'slug' => 'modelo-sem-assinatura',
        'escopo' => 'notificacao_geral',
        'remetente_email' => 'teste@imobiliaria.com',
        'remetente_nome' => 'Teste',
        'assunto' => 'Assunto',
        'corpo' => '<p>Só o corpo</p>',
        'ativo' => true,
        'sistema' => false,
    ]);

    expect($modelo->corpoComAssinatura())->toBe('<p>Só o corpo</p>');
});

test('corpoComAssinatura skips inactive signature', function () {
    $assinatura = criarAssinatura(['ativo' => false]);
    $modelo = ModeloEmail::create([
        'nome' => 'Modelo Assinatura Inativa',
        'slug' => 'modelo-assinatura-inativa',
        'escopo' => 'notificacao_geral',
        'remetente_email' => 'teste@imobiliaria.com',
        'remetente_nome' => 'Teste',
        'assunto' => 'Assunto',
        'corpo' => '<p>Corpo</p>',
        'ativo' => true,
        'sistema' => false,
        'assinatura_email_id' => $assinatura->id,
    ]);

    expect($modelo->corpoComAssinatura())->toBe('<p>Corpo</p>');
});

test('deleting assinatura nulls fk and show page still works', function () {
    $assinatura = criarAssinatura();

    $this->post(route('configuracoes.emails.store'), [
        'nome' => 'Modelo Após Excluir Assinatura',
        'escopo' => 'notificacao_geral',
        'remetente_email' => 'teste@imobiliaria.com',
        'remetente_nome' => 'Teste',
        'assunto' => 'Assunto',
        'corpo' => '<p>Corpo</p>',
        'ativo' => true,
        'assinatura_email_id' => $assinatura->id,
    ]);

    $modelo = ModeloEmail::where('nome', 'Modelo Após Excluir Assinatura')->firstOrFail();

    $this->delete(route('configuracoes.assinaturas.destroy', $assinatura));

    $modelo->refresh();
    expect($modelo->assinatura_email_id)->toBeNull();

    $this->get(route('configuracoes.emails.show', $modelo))->assertOk();
    $this->get(route('configuracoes.emails.edit', $modelo))->assertOk();
});

test('preview page shows signature content', function () {
    $assinatura = criarAssinatura();
    $this->seed(ModeloEmailSeeder::class);

    $modelo = ModeloEmail::where('slug', 'boas-vindas-locatario')->firstOrFail();
    $modelo->update(['assinatura_email_id' => $assinatura->id]);

    $response = $this->get(route('configuracoes.emails.show', $modelo->fresh()));

    $response->assertOk();
    $response->assertSee('João Corretor');
    $response->assertSee('Assinatura Teste');
});

test('emails index lists assinaturas section', function () {
    criarAssinatura(['nome' => 'Visível na Lista']);

    $response = $this->get(route('configuracoes.emails.index'));

    $response->assertOk();
    $response->assertSee('Assinaturas de E-mail');
    $response->assertSee('Visível na Lista');
});
