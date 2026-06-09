<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela contas e cartões
     */
    public function up(): void
    {
        Schema::create('account_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('name');                                 // Instituição financeira

            $table->tinyInteger('account_type_id')->unsigned();     // Conta corrente, Cartão, Dinheiro
            $table->foreign('account_type_id')
                ->references('id')
                ->on('account_types');

            $table->decimal('balance', 10, 2)->nullable();          // Saldo Conta Corrente ou Dinheiro
            $table->decimal('credit_limit', 10, 2)->nullable();     // Limite do Cartão de Crédito
            $table->decimal('available_limit', 10, 2)->nullable();  // Valor disponível do Cartão de Crédito
            $table->integer('closing_day')->nullable();             // Dia fechamento da fatura
            $table->integer('due_day')->nullable();                 // Dia do pagamento da fatura

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_cards');
    }
};
