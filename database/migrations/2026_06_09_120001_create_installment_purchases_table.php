<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela de Compras a Prazo.
     */
    public function up(): void
    {
        Schema::create('installment_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('account_card_id')->constrained();
            $table->string('description', 255);
            $table->decimal('total_amount', 10, 2);         // Valor total da compra
            $table->tinyInteger('total_installments');     // Quantidade de parcelas
            $table->date('purchase_date');                  // Data da compra

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installment_purchases');
    }
};
