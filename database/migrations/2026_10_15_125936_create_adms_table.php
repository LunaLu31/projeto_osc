<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Espelha a tabela `adms` do script físico (database/schema/uoscc_redecam.sql).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adms', function (Blueprint $table) {
            $table->integer('id_admin', true);
            $table->string('nome', 100);
            $table->string('email', 150)->unique();
            $table->string('senha_hash', 255);
            $table->timestamp('data_cadastro')->nullable()->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adms');
    }
};
