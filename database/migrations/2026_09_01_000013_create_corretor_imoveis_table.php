<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corretor_imoveis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corretor_id')->constrained('corretores')->onDelete('cascade');
            $table->foreignId('imovel_id')->constrained('imoveis')->onDelete('cascade');
            $table->date('data_atribuicao');
            $table->date('data_fim')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['imovel_id', 'ativo']);
            $table->index(['corretor_id', 'ativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corretor_imoveis');
    }
};