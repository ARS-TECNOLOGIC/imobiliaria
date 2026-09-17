<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corretores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pessoa_id')->constrained('pessoas')->onDelete('cascade');
            // Opcional: preenchido apenas se o corretor também faz login no sistema.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('creci', 20)->nullable()->unique();
            $table->decimal('percentual_comissao', 5, 2)->default(0.00);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index('ativo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corretores');
    }
};