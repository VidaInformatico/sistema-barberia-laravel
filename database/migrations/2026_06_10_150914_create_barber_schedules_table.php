<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Horarios semanales del barbero (ej: Lunes 09:00-18:00)
        Schema::create('barber_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barber_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('day_of_week'); // 0=Domingo, 1=Lunes ... 6=Sábado
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['barber_id', 'day_of_week']);
        });

        // Bloqueos específicos de fechas/horas (vacaciones, ausencias, etc.)
        Schema::create('barber_blocked_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barber_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('start_time')->nullable(); // null = día completo bloqueado
            $table->time('end_time')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barber_blocked_times');
        Schema::dropIfExists('barber_schedules');
    }
};
