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
        Schema::table('documentos', function (Blueprint $table) {
            $table->enum('categoria_armazenamento', [
                'CONTAS_DE_CONSUMO',
                'DOCUMENTOS_ASSINADOS',
                'DOCUMENTOS_PARA_ASSINAR',
                'LOCADOR',
                'LOCATARIO',
                'RECIBOS',
                'SEGUROS',
                'REAJUSTES_ANUAIS',
                'COMPROVANTES_RENDIMENTOS_ANUAIS',
                'FIADOR',
            ])->after('documentavel_id');
        });
    }

    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropColumn('categoria_armazenamento');
        });
    }
};
