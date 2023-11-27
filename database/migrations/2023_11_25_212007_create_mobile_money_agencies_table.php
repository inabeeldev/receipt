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
        Schema::create('mobile_money_agencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('transaction_type');
            $table->string('chosen_product');
            $table->bigInteger('customer_mobile');
            $table->decimal('transaction_amount', 10, 2);
            $table->decimal('withdrawals_fee', 10, 2);
            $table->bigInteger('wave_transaction');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_money_agencies');
    }
};
