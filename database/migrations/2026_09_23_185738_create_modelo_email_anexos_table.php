<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modelo_email_anexos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modelo_email_id')->constrained('modelos_email')->cascadeOnDelete();
            $table->string('nome_original', 255);
            $table->string('caminho_arquivo', 500);
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('tamanho_bytes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modelo_email_anexos');
    }
};
