<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_policies', function (Blueprint $table) {

            $table->id();

            // Reservas
            $table->integer('max_days_in_advance')->default(30);
            $table->integer('min_hours_before_booking')->default(1);

            // Cancelaciones
            $table->integer('cancel_before_hours')->default(24);
            $table->boolean('allow_client_cancellation')->default(true);

            // Tolerancia
            $table->integer('grace_period_minutes')->default(10);

            // Inasistencias
            $table->boolean('mark_no_show_after_grace')->default(true);

            // Estado
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_policies');
    }
};