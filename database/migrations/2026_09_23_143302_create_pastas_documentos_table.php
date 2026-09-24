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
        Schema::create('pastas_documentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('slug', 100)->unique();
            $table->text('descricao')->nullable();
            $table->boolean('ativa')->default(true);
            $table->unsignedInteger('ordem')->default(0);
            $table->boolean('sistema')->default(false);
            $table->timestamps();

            $table->index(['ativa', 'ordem']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pastas_documentos');
    }
};
