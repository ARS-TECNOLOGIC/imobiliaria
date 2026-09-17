<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            // Relação polimórfica: um documento pode pertencer a uma pessoa,
            // um imóvel, um contrato ou uma fatura (documentavel_type + documentavel_id).
            $table->morphs('documentavel');
            $table->enum('tipo_documento', [
                'CONTRATO_ASSINADO',
                'COMPROVANTE_PAGAMENTO',
                'LAUDO_VISTORIA',
                'RG_CPF',
                'COMPROVANTE_RESIDENCIA',
                'COMPROVANTE_RENDA',
                'FOTO_IMOVEL',
                'APOLICE_SEGURO',
                'OUTRO',
            ]);
            $table->string('nome_original', 255);
            $table->string('caminho_arquivo', 500);
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('tamanho_bytes')->nullable();
            $table->string('descricao', 255)->nullable();
            $table->foreignId('enviado_por_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('tipo_documento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};