<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imovel_id')->constrained('imoveis')->onDelete('cascade');
            // Pessoa que recebe o repasse (normalmente o locador, mas pode ser um
            // procurador/beneficiário diferente do titular do imóvel).
            $table->foreignId('favorecido_id')->constrained('pessoas')->onDelete('cascade');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->integer('dia_vencimento');
            $table->enum('garantia', ['FIADOR', 'SEGURO_FIANCA', 'TITULO_CAPITALIZACAO', 'CAUCAO', 'SEM_GARANTIA']);
            $table->decimal('valor_aluguel', 10, 2);
            $table->decimal('valor_condominio', 10, 2)->default(0.00);
            $table->decimal('valor_iptu', 10, 2)->default(0.00);
            $table->string('parcela_iptu', 20)->nullable();
            $table->decimal('valor_seguro', 10, 2)->default(0.00);
            $table->decimal('taxa_adm_percentual', 5, 2)->default(8.00);
            $table->enum('status', ['ATIVO', 'ENCERRADO', 'SUSPENSO'])->default('ATIVO');
            $table->timestamps();

            $table->index('status');
            $table->index('dia_vencimento');
            $table->index(['imovel_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};