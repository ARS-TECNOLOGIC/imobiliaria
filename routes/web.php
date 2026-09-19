<?php

use App\Http\Controllers\ContratoController;
use App\Http\Controllers\ContratoParteController;
use App\Http\Controllers\ContratoSeguroFiancaController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\FaturaController;
use App\Http\Controllers\ImovelController;
use App\Http\Controllers\ImovelServicoCondominioController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\RepasseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
