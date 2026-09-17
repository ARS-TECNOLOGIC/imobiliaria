<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrato_partes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained('contratos')->onDelete('cascade');
            $table->foreignId('pessoa_id')->constrained('pessoas')->onDelete('cascade');
            $table->enum('papel', ['LOCADOR_TITULAR', 'LOCADOR_CONJUGE', 'LOCATARIO_TITULAR', 'LOCATARIO_CONJUGE', 'FIADOR_TITULAR', 'FIADOR_CONJUGE']);
            $table->boolean('assina_contrato')->default(true);
            $table->timestamps();

            $table->unique(['contrato_id', 'pessoa_id', 'papel']);
            $table->index('papel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrato_partes');
    }
};