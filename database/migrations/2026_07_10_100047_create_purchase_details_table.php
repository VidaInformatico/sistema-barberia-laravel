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
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->text('product_name')->nullable();
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->integer('itbis_rate')->default(0);      // itbistasa
            $table->decimal('discount_rate', 5, 2)->default(0);   // descuentotasa

            $table->decimal('itbis_amount', 15, 2)->default(0);   // itbisvalor
            $table->decimal('gross_subtotal', 15, 2)->default(0); // subtotalBruto
            $table->decimal('discount_amount', 15, 2)->default(0); // descuentovalor
            $table->decimal('net_subtotal', 15, 2)->default(0);   // subtotalneto
            $table->decimal('total', 15, 2)->default(0);          // total

            $table->boolean('is_exempt')->default(false);         // es_exento
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_details');
    }
};
