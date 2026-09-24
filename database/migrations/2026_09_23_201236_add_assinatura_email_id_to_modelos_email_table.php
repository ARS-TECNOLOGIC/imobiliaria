<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modelos_email', function (Blueprint $table) {
            $table->foreignId('assinatura_email_id')
                ->nullable()
                ->after('descricao')
                ->constrained('assinaturas_email')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('modelos_email', function (Blueprint $table) {
            $table->dropForeign(['assinatura_email_id']);
            $table->dropColumn('assinatura_email_id');
        });
    }
};
