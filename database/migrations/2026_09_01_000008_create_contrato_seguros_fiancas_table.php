<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrato_seguros_fiancas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained('contratos')->onDelete('cascade');
            $table->enum('tipo', ['SEGURO_INCENDIO', 'FIANCA_LOCATICA', 'TITULO_CAPITALIZACAO']);
            $table->string('seguradora', 100);
            $table->string('numero_apolice', 100)->nullable();
            $table->decimal('valor_cobertura', 10, 2)->nullable();
            $table->decimal('valor_premio', 10, 2)->nullable();
            $table->date('data_inicio');
            $table->date('data_vencimento_apolice');
            $table->enum('status', ['ATIVO', 'A_RENOVAR', 'EXPIRADO', 'CANCELADO'])->default('ATIVO');
            $table->timestamps();

            $table->index('status');
            $table->index('data_vencimento_apolice');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrato_seguros_fiancas');
    }
};