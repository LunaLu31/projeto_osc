<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Espelha a tabela `manifestacoes_interesses` do script físico (database/schema/uoscc_redecam.sql).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manifestacoes_interesses', function (Blueprint $table) {
            $table->integer('id_manifestacao', true);
            $table->integer('id_demanda');
            $table->string('nome_apoiador', 100);
            $table->string('telefone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->enum('tipo_interesse', ['Apoiador/Doação', 'Voluntário', 'Aluno']);
            $table->text('mensagem')->nullable();
            $table->timestamp('data_manifestacao')->nullable()->useCurrent();

            $table->foreign('id_demanda')->references('id_demanda')->on('demandas')
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->unique(['id_demanda', 'telefone'], 'uk_demanda_telefone');
            $table->unique(['id_demanda', 'email'], 'uk_demanda_email');
            $table->index('id_demanda', 'idx_id_demanda');
            $table->index('tipo_interesse', 'idx_tipo_interesse');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE manifestacoes_interesses
                ADD CONSTRAINT chk_contato_apoiador CHECK (telefone IS NOT NULL OR email IS NOT NULL)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('manifestacoes_interesses');
    }
};
