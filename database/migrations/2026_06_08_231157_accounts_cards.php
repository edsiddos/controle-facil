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
        //Cria a tabela contas e cartões
        Schema::create('accounts_cards', function(Blueprint $table){
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('name');
            $table->foreignId('account_type_id')->constrained();
            $table->decimal('current_balance', 10, 2);

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_cards');
    }
};
