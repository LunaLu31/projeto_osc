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
        Schema::create('manifestacoes_interesses', function (Blueprint $table) {
        $table->id('id_manifestacao');
        $table->foreignId('id_demanda')->constrained('demandas', 'id_demanda')->cascadeOnDelete();
        $table->string('nome_apoiador', 150);
        $table->string('telefone', 20)->nullable();
        $table->string('email', 150)->nullable();
        $table->string('tipo_interesse', 50)->nullable();
        $table->text('mensagem')->nullable();
        $table->timestamp('data_manifestacao')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manifestacoes_interesses');
    }
};
