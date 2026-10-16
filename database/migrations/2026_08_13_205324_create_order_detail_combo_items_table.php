<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_detail_combo_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_detail_id')
                  ->constrained('order_details')
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

    public function down(): void
    {
        Schema::dropIfExists('order_detail_combo_items');
    }
};
