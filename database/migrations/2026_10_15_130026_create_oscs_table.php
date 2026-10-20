<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Espelha a tabela `oscs` do script físico (database/schema/uoscc_redecam.sql).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oscs', function (Blueprint $table) {
            $table->integer('id_osc', true);
            $table->integer('id_admin');
            $table->string('nome', 150);
            $table->string('area_atuacao', 100);
            $table->text('descricao')->nullable();
            $table->string('endereco', 255)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('email', 150)->unique();
            $table->string('senha_hash', 255);
            $table->timestamp('data_cadastro')->nullable()->useCurrent();

            $table->foreign('id_admin')->references('id_admin')->on('adms')
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->index('id_admin', 'idx_id_admin');
            $table->index('area_atuacao', 'idx_area_atuacao');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oscs');
    }
};
