<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('image_url');
            $table->string('subtitle')->nullable();
            $table->string('title');
            $table->text('text')->nullable();
            
            $table->string('btn1_text');
            $table->string('btn1_url');
            $table->string('btn1_icon')->nullable();

            $table->string('btn2_text')->nullable();
            $table->string('btn2_url')->nullable();
            $table->string('btn2_icon')->nullable();

            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};
