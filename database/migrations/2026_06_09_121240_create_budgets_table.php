<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Definição de Orçamentos
     */
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();

            $table->smallInteger('category_id')->unsigned();
            $table->foreign('category_id')
                ->references('id')
                ->on('categories');

            $table->decimal('amount_limit', 10, 2); // Limite de gasto
            $table->string('year_month', 7);        // Período do gasto ANO e MÊS (2026-06)


            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
