<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela metas (Poupança, Projeto a Longo prazo)
     */
    public function up(): void
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('name');                     // Nome da meta
            $table->decimal('target_amount', 10, 2);    // Valor objetivo
            $table->decimal('current_amount', 10, 2);   // Valor já "guardado"
            $table->date('deadline')->nullable();       // Objetivo até que data

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
