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
        Schema::create('catering_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->mediumText('service_description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catering_services');
    }
};
