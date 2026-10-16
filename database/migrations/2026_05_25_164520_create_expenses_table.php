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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_register_id')
                ->constrained('cash_registers')
                ->onDelete('restrict');

            $table->foreignId('payment_method_id')
                ->constrained('payment_methods')
                ->onDelete('restrict');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('restrict');

            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();

            $table->decimal('amount', 15, 2);
            $table->decimal('itbis', 15, 2)->default(0);
            $table->decimal('other_taxes', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);

            $table->string('concept');
            $table->text('description')->nullable();
            $table->date('expense_date');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
