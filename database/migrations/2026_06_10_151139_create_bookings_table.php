<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number')->unique();
            $table->enum('payment_method', ['local', 'paypal', 'mercadopago'])->default('local');
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('barber_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', [
                'pending',      // Pendiente de confirmación
                'confirmed',    // Confirmada
                'in_progress',  // En curso
                'completed',    // Finalizada
                'cancelled',    // Cancelada
                'no_show',      // No se presentó
            ])->default('pending');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->text('notes')->nullable();             // notas del cliente
            $table->text('admin_notes')->nullable();       // notas internas del admin
            $table->string('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('arrived_at')->nullable();   // hora real de llegada
            $table->timestamps();
            $table->softDeletes();
 
            $table->index(['booking_date', 'barber_id']);
            $table->index(['client_id', 'status']);
        });
 
        // Servicios incluidos en cada reserva (una reserva puede tener varios servicios)
        Schema::create('booking_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->string('service_name');          // snapshot del nombre
            $table->integer('duration_minutes');     // snapshot de la duración
            $table->decimal('price', 10, 2);         // snapshot del precio
            $table->timestamps();
        });
 
        // Historial de cambios de estado de la reserva
        Schema::create('booking_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        // Ventas en mostrador
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_number')->unique();
            $table->foreignId('client_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('barber_id')->nullable()->constrained()->nullOnDelete(); // barbero que realizó la venta
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete(); // venta asociada a reserva
            $table->foreignId('cash_register_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->decimal('gross_total', 10, 2)->default(0);
            $table->decimal('tax_total', 10, 2)->default(0);
            $table->decimal('net_total', 10, 2)->default(0);
            $table->decimal('discount_total', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('credit', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('change', 10, 2)->default(0);
            $table->timestamp('paid_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->string('product_name');
            $table->integer('quantity');
            $table->decimal('cost', 10, 2)->default(0);
            $table->decimal('price', 10, 2);
            $table->integer('itbis_rate')->default(0);
            $table->decimal('discount_rate', 5, 2)->default(0);

            $table->decimal('itbis_amount', 15, 2)->default(0);
            $table->decimal('gross_subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('net_subtotal', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            $table->boolean('is_exempt')->default(false);
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('booking_status_history');
        Schema::dropIfExists('booking_services');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
    }
};
 