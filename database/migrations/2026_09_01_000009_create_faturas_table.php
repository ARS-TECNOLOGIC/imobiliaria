<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained('contratos')->onDelete('cascade');
            $table->date('referencia');
            $table->date('data_vencimento');
            $table->decimal('valor_aluguel', 10, 2);
            $table->decimal('valor_condominio', 10, 2)->default(0.00);
            $table->decimal('valor_iptu', 10, 2)->default(0.00);
            $table->string('parcela_iptu', 20)->nullable();
            $table->decimal('valor_seguro', 10, 2)->default(0.00);
            $table->decimal('valor_taxa_extra', 10, 2)->default(0.00);
            $table->string('descricao_taxa_extra', 255)->nullable();
            $table->decimal('valor_desconto', 10, 2)->default(0.00);
            $table->string('descricao_desconto', 255)->nullable();
            $table->decimal('valor_multa_juros', 10, 2)->default(0.00);
            $table->decimal('valor_pago', 10, 2)->nullable();
            $table->integer('dias_atraso')->default(0);
            $table->enum('status_pagamento', ['PENDENTE', 'PAGO', 'PAGO_COM_ATRASO', 'ATRASADO', 'CANCELADO'])->default('PENDENTE');
            $table->date('data_recebimento')->nullable();
            $table->string('banco_recebedor', 100)->nullable();
            $table->text('descricao_boleto')->nullable();
            $table->timestamps();

            $table->unique(['contrato_id', 'referencia']);
            $table->index('status_pagamento');
            $table->index('data_vencimento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faturas');
    }
};