<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrato_historico_valores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained('contratos')->onDelete('cascade');
            $table->enum('tipo_campo', ['ALUGUEL', 'IPTU', 'CONDOMINIO', 'SEGURO']);
            $table->decimal('valor_anterior', 10, 2);
            $table->decimal('valor_novo', 10, 2);
            $table->string('motivo', 255)->nullable();
            $table->foreignId('alterado_por_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['contrato_id', 'tipo_campo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrato_historico_valores');
    }
};