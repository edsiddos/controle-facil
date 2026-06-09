<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria tabela de Transações despesas/receitas e parcelas.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('account_card_id')->constrained();

            $table->smallInteger('category_id')->unsigned();
            $table->foreign('category_id')
            ->references('id')
            ->on('categories');

            $table->foreignId('installment_purchase_id')->nullable(); // ID da compra parcelada
            $table->string('description');
            $table->decimal('amount', 10, 2);                       // valor pago
            $table->date('transaction_date');
            $table->boolean('is_recurring');                        // Pagamento recorrente (serviço de assinatura)
            $table->tinyInteger('installment_number')->nullable();  // Número da parcela
            $table->enum('status', ['A PAGAR', 'PAGO']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
