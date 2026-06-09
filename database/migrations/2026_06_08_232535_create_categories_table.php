<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cria tabela de categoria de receita ou despesas
        Schema::create('categories', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->foreignId('user_id')->constrained()->nullable(); // Classificar transações (Alimentação, Transporte, Lazer, etc.) e permitir categorias personalizadas.
            $table->string('name');
            $table->string('type', 1);                               // R: Receita ou D: Despesa
            $table->string('icon', 50)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
