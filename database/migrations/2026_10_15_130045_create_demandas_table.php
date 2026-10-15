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
        Schema::create('demandas', function (Blueprint $table) {
         $table->id('id_demanda');
         $table->foreignId('id_osc')->constrained('oscs', 'id_osc')->cascadeOnDelete();
         $table->string('titulo', 150);
         $table->string('categoria', 100)->nullable();
         $table->text('descricao')->nullable();
         $table->integer('quantidade')->nullable();
         $table->date('prazo')->nullable();
         $table->string('status')->default('aberta');
         $table->string('urgencia', 20)->nullable();
         $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandas');
    }
};
