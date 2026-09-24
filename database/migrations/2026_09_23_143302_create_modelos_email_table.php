<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('modelos_email', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->string('slug', 100)->unique();
            $table->string('escopo', 100); // ex: 'contrato_locacao', 'vencimento_fatura', 'boas_vindas'
            $table->string('remetente_email', 150);
            $table->string('remetente_nome', 100);
            $table->string('assunto', 255);
            $table->longText('corpo');
            $table->json('variaveis_disponiveis')->nullable(); // JSON array com nomes das variáveis e descrições
            $table->boolean('ativo')->default(true);
            $table->boolean('sistema')->default(false);
            $table->text('descricao')->nullable();
            $table->timestamps();

            $table->index(['escopo', 'ativo']);
            $table->index(['slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modelos_email');
    }
};
