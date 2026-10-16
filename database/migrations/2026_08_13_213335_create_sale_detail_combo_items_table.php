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
        Schema::create('sale_item_combo_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_item_id')
                  ->constrained('sale_items')
                  ->onDelete('cascade');

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('restrict');

            $table->string('product_name');
            $table->decimal('unit_quantity', 10, 2);
            $table->decimal('total_quantity', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_item_combo_items');
    }
};
