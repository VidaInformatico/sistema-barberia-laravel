<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->enum('type', ['product', 'service'])->default('product');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('sku')->unique()->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->integer('tax')->default(5);
            $table->integer('sort_order')->default(0);
            $table->integer('stock_min')->default(5);
            $table->string('image')->nullable();
            $table->boolean('is_exempt')->default(false);
            $table->boolean('is_inventoriable')->default(true);
            $table->boolean('is_active')->default(true);
            $table->enum('type', ['product', 'service', 'combo'])->default('product');
            $table->integer('duration_minutes')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('barber_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barber_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('custom_price', 10, 2)->nullable();
            $table->timestamps();
 
            $table->unique(['barber_id', 'product_id']);
        });

        // Movimientos de inventario
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // quien realizó el movimiento
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['in', 'out', 'adjustment']);
            $table->integer('quantity');
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->string('reason')->nullable();
            $table->nullableMorphs('reference');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('barber_services');
    }
};
