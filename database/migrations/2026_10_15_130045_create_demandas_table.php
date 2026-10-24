<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Espelha a tabela `demandas` do script físico (database/schema/uoscc_redecam.sql).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demandas', function (Blueprint $table) {
            $table->integer('id_demanda', true);
            $table->integer('id_osc');
            $table->string('titulo', 150);
            $table->string('categoria', 100);
            $table->text('descricao');
            $table->integer('quantidade')->default(1);
            $table->date('prazo')->nullable();
            $table->enum('status', ['Aberta', 'Parcial', 'Concluída'])->default('Aberta');
            $table->boolean('urgencia')->default(false);
            $table->timestamp('data_criacao')->nullable()->useCurrent();
            $table->timestamp('data_atualizacao')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->foreign('id_osc')->references('id_osc')->on('oscs')
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->index('id_osc', 'idx_id_osc');
            $table->index('status', 'idx_status');
            $table->index('categoria', 'idx_categoria');
            $table->index('urgencia', 'idx_urgencia');
            $table->index('prazo', 'idx_prazo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandas');
    }
};
