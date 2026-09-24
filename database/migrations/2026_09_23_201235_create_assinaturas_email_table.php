<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assinaturas_email', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->text('conteudo');
            $table->boolean('ativo')->default(true);
            $table->boolean('sistema')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assinaturas_email');
    }
};
