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
        Schema::create('oscs', function (Blueprint $table) {
    $table->id('id_osc');
    $table->foreignId('id_admin')->constrained('adms', 'id_admin');
    $table->string('nome', 150);
    $table->string('area_atuacao', 100)->nullable();
    $table->text('descricao')->nullable();
    $table->string('endereco')->nullable();
    $table->string('telefone', 20)->nullable();
    $table->string('email', 150)->unique();
    $table->string('senha_hash');
    $table->timestamp('data_cadastro')->useCurrent();
     });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oscs');
    }
};
