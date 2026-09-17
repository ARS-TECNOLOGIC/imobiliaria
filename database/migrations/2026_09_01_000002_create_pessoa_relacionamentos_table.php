<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pessoa_relacionamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pessoa_id')->constrained('pessoas')->onDelete('cascade');
            $table->foreignId('conjuge_id')->constrained('pessoas')->onDelete('cascade');
            $table->enum('tipo_vinculo', ['CONJUGE', 'COMPANHEIRO_UNIAO_ESTAVEL']);
            $table->timestamps();

            $table->unique(['pessoa_id', 'conjuge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pessoa_relacionamentos');
    }
};