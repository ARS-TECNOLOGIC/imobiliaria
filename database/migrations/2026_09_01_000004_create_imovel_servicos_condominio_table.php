<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imovel_servicos_condominio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imovel_id')->constrained('imoveis')->onDelete('cascade');
            $table->enum('servico', ['AGUA', 'GAS', 'LUZ', 'INTERNET', 'PORTARIA', 'ACADEMIA', 'OUTRO']);
            $table->string('descricao_outro', 100)->nullable();
            $table->enum('tipo_cobranca', ['INCLUSO_NO_CONDOMINIO', 'INDIVIDUALIZADO_BOLETO', 'CONTA_SEPARADA'])->default('INCLUSO_NO_CONDOMINIO');
            $table->string('observacao', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imovel_servicos_condominio');
    }
};