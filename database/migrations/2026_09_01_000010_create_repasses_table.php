<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repasses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fatura_id')->unique()->constrained('faturas')->onDelete('cascade');
            $table->decimal('valor_taxa_adm', 10, 2);
            $table->decimal('valor_retido', 10, 2)->default(0.00);
            $table->string('descricao_retido', 255)->nullable();
            $table->decimal('valor_custos', 10, 2)->default(0.00);
            $table->string('descricao_custos', 255)->nullable();
            $table->decimal('valor_total_repasse', 10, 2);
            $table->date('data_limite_repasse');
            $table->date('data_repasse')->nullable();
            $table->enum('status', ['PENDENTE', 'EFETUADO', 'CANCELADO'])->default('PENDENTE');
            $table->timestamps();

            $table->index('status');
            $table->index('data_limite_repasse');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repasses');
    }
};