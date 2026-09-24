<?php

use App\Http\Controllers\AssinaturaEmailController;
use App\Http\Controllers\ConfiguracaoController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\ContratoParteController;
use App\Http\Controllers\ContratoSeguroFiancaController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\FaturaController;
use App\Http\Controllers\ImovelController;
use App\Http\Controllers\ImovelServicoCondominioController;
use App\Http\Controllers\ModeloEmailAnexoController;
use App\Http\Controllers\ModeloEmailController;
use App\Http\Controllers\PastaDocumentoController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\RepasseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('pessoas/buscar', [PessoaController::class, 'buscar'])->name('pessoas.buscar');
Route::post('pessoas/ajax', [PessoaController::class, 'storeAjax'])->name('pessoas.ajax');

Route::resource('pessoas', PessoaController::class);

Route::resource('imoveis', ImovelController::class)->parameters(['imoveis' => 'imovel']);
Route::post('imoveis/{imovel}/servicos', [ImovelServicoCondominioController::class, 'store'])->name('imoveis.servicos.store');
Route::delete('imovel-servicos/{servico}', [ImovelServicoCondominioController::class, 'destroy'])->name('imovel-servicos.destroy');

Route::resource('contratos', ContratoController::class);

Route::post('contratos/{contrato}/partes', [ContratoParteController::class, 'store'])->name('contratos.partes.store');
Route::delete('contrato-partes/{parte}', [ContratoParteController::class, 'destroy'])->name('contrato-partes.destroy');

Route::post('contratos/{contrato}/seguros', [ContratoSeguroFiancaController::class, 'store'])->name('contratos.seguros.store');
Route::delete('contrato-seguros/{seguro}', [ContratoSeguroFiancaController::class, 'destroy'])->name('contrato-seguros.destroy');

Route::resource('faturas', FaturaController::class);
Route::put('repasses/{repasse}', [RepasseController::class, 'update'])->name('repasses.update');

Route::post('documentos', [DocumentoController::class, 'store'])->name('documentos.store');
Route::get('documentos/{documento}/download', [DocumentoController::class, 'download'])->name('documentos.download');
Route::delete('documentos/{documento}', [DocumentoController::class, 'destroy'])->name('documentos.destroy');

Route::prefix('configuracoes')->name('configuracoes.')->group(function () {
    // Sistema (configurações chave/valor)
    Route::get('/', [ConfiguracaoController::class, 'index'])->name('index');
    Route::put('/multiplo', [ConfiguracaoController::class, 'updateMultiplo'])->name('update-multiplo');
    Route::post('/', [ConfiguracaoController::class, 'store'])->name('store');
    Route::put('/{configuracao}', [ConfiguracaoController::class, 'update'])->name('update');
    Route::delete('/{configuracao}', [ConfiguracaoController::class, 'destroy'])->name('destroy');

    // Documentos (pastas)
    Route::get('/documentos', [PastaDocumentoController::class, 'index'])->name('documentos.index');
    Route::post('/documentos', [PastaDocumentoController::class, 'store'])->name('documentos.store');
    Route::put('/documentos/{pasta}', [PastaDocumentoController::class, 'update'])->name('documentos.update');
    Route::delete('/documentos/{pasta}', [PastaDocumentoController::class, 'destroy'])->name('documentos.destroy');
    Route::post('/documentos/reordenar', [PastaDocumentoController::class, 'reordenar'])->name('documentos.reordenar');

    // Assinaturas de e-mail
    Route::post('/assinaturas', [AssinaturaEmailController::class, 'store'])->name('assinaturas.store');
    Route::put('/assinaturas/{assinatura}', [AssinaturaEmailController::class, 'update'])->name('assinaturas.update');
    Route::delete('/assinaturas/{assinatura}', [AssinaturaEmailController::class, 'destroy'])->name('assinaturas.destroy');

    // E-mails (modelos)
    Route::get('/emails', [ModeloEmailController::class, 'index'])->name('emails.index');
    Route::post('/emails', [ModeloEmailController::class, 'store'])->name('emails.store');
    Route::get('/emails/variaveis', [ModeloEmailController::class, 'variaveisPorEscopo'])->name('emails.variaveis');
    Route::post('/emails/imagens', [ModeloEmailController::class, 'uploadImagem'])->name('emails.imagens.store');
    Route::get('/emails/{modelo}', [ModeloEmailController::class, 'show'])->name('emails.show');
    Route::get('/emails/{modelo}/edit', [ModeloEmailController::class, 'edit'])->name('emails.edit');
    Route::put('/emails/{modelo}', [ModeloEmailController::class, 'update'])->name('emails.update');
    Route::delete('/emails/{modelo}', [ModeloEmailController::class, 'destroy'])->name('emails.destroy');
    Route::post('/emails/{modelo}/anexos', [ModeloEmailAnexoController::class, 'store'])->name('emails.anexos.store');
    Route::get('/emails/{modelo}/anexos/{anexo}/download', [ModeloEmailAnexoController::class, 'download'])->name('emails.anexos.download');
    Route::delete('/emails/{modelo}/anexos/{anexo}', [ModeloEmailAnexoController::class, 'destroy'])->name('emails.anexos.destroy');
});
