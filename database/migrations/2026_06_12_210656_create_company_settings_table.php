<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();

            $table->string('currency_code', 3)->default('USD'); // ej: USD, EUR, PEN
            $table->string('timezone')->default('UTC');
            $table->string('logo_path')->nullable();
            $table->string('hours')->default('Lun - Sáb: 9:00 - 20:00');

            $table->json('social_links')->nullable();

            $table->json('extra_config')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
