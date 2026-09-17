<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pessoas', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->string('cpf_cnpj', 20)->unique();
            $table->string('rg', 20)->nullable();
            $table->string('rg_orgao_emissor', 20)->nullable();
            $table->date('data_nascimento')->nullable();
            $table->enum('estado_civil', ['SOLTEIRO', 'CASADO', 'DIVORCIADO', 'VIUVO', 'UNIAO_ESTAVEL'])->default('SOLTEIRO');
            $table->enum('regime_bens', ['COMUNHAO_PARCIAL', 'COMUNHAO_UNIVERSAL', 'SEPARACAO_TOTAL', 'PARTICIPACAO_FINAL_AQUESTOS'])->nullable();
            $table->string('email', 150)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('cep', 10)->nullable();
            $table->string('logradouro', 150)->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('complemento', 50)->nullable();
            $table->string('bairro', 100)->nullable();
            $table->string('cidade', 100)->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('tipo_chave_pix', 20)->nullable();
            $table->string('chave_pix', 100)->nullable();
            $table->string('banco', 50)->nullable();
            $table->string('agencia', 20)->nullable();
            $table->string('conta', 20)->nullable();
            $table->enum('tipo_conta', ['corrente', 'poupanca'])->default('corrente');
            $table->softDeletes();
            $table->timestamps();

            $table->index('nome');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pessoas');
    }
};