<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_photos', function (Blueprint $table) {

            $table->id();

            $table->foreignId('barber_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title')->nullable();

            $table->text('description')->nullable();

            $table->string('image');

            $table->boolean('is_featured')->default(false);

            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_photos');
    }
};