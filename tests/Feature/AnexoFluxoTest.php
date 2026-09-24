<?php

use App\Models\ModeloEmail;
use Database\Seeders\ModeloEmailSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

test('full anexo browser flow: upload shows in edit list after redirect', function () {
    Storage::fake('privada');
    $this->seed(ModeloEmailSeeder::class);

    $modelo = ModeloEmail::where('slug', 'boas-vindas-locatario')->firstOrFail();

    $response = $this->post(route('configuracoes.emails.anexos.store', $modelo), [
        'arquivo' => UploadedFile::fake()->create('contrato.pdf', 100, 'application/pdf'),
    ]);

    $response->assertRedirect(route('configuracoes.emails.edit', $modelo));
    $response->assertSessionHas('sucesso');
    expect($modelo->anexos()->count())->toBe(1);

    $edit = $this->get(route('configuracoes.emails.edit', $modelo));
    $edit->assertOk();
    $edit->assertSee('Anexo enviado com sucesso.');
    $edit->assertSee('contrato.pdf');
    $edit->assertDontSee('Nenhum anexo neste modelo.');
});

test('anexo form on edit page posts to anexos.store not update', function () {
    $this->seed(ModeloEmailSeeder::class);
    $modelo = ModeloEmail::where('slug', 'boas-vindas-locatario')->firstOrFail();

    $html = $this->get(route('configuracoes.emails.edit', $modelo))->getContent();

    expect($html)->toContain('enctype="multipart/form-data"');
    expect($html)->toContain(route('configuracoes.emails.anexos.store', $modelo));
    expect($html)->toContain('name="arquivo"');

    preg_match('/<form action="[^"]*anexos[^"]*"[^>]*>/', $html, $m);
    expect($m[0])->not->toContain('form="formUpdateModelo"');
});

test('anexo store validates file and returns errors on edit page', function () {
    $this->seed(ModeloEmailSeeder::class);
    $modelo = ModeloEmail::where('slug', 'boas-vindas-locatario')->firstOrFail();

    $response = $this->post(route('configuracoes.emails.anexos.store', $modelo), [
        'arquivo' => UploadedFile::fake()->create('malicioso.php', 10, 'text/plain'),
    ]);

    $response->assertSessionHasErrors('arquivo');
    $response->assertRedirect();
    expect($modelo->anexos()->count())->toBe(0);
});

test('show page lists anexos after upload', function () {
    Storage::fake('privada');
    $this->seed(ModeloEmailSeeder::class);
    $modelo = ModeloEmail::where('slug', 'boas-vindas-locatario')->firstOrFail();

    $this->post(route('configuracoes.emails.anexos.store', $modelo), [
        'arquivo' => UploadedFile::fake()->create('contrato.pdf', 50, 'application/pdf'),
    ]);

    $show = $this->get(route('configuracoes.emails.show', $modelo));
    $show->assertOk();
    $show->assertSee('contrato.pdf');
    $show->assertSee('Anexos');
});
