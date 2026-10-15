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
        Schema::create('adms', function (Blueprint $table) {
    $table->id('id_admin');
    $table->string('nome', 100);
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
        Schema::dropIfExists('adms');
    }
};
