<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imoveis', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->foreignId('locador_id')->constrained('pessoas')->onDelete('cascade');
            $table->enum('tipo', ['RESIDENCIAL', 'COMERCIAL'])->default('RESIDENCIAL');
            $table->string('cep', 10)->nullable();
            $table->string('logradouro', 150);
            $table->string('numero', 20);
            $table->string('complemento', 50)->nullable();
            $table->string('bairro', 100)->nullable();
            $table->string('cidade', 100)->default('Bauru');
            $table->string('uf', 2)->default('SP');
            $table->boolean('possui_condominio')->default(false);
            $table->string('nome_condominio', 150)->nullable();
            $table->string('administradora_condominio', 150)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('cidade');
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imoveis');
    }
};