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
        Schema::create('event_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('standard'); // vip, dancefloor, balcony, etc
            $table->enum('seating_mode', ['seated', 'standing'])->default('standing');
            $table->unsignedInteger('capacity')->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedInteger('rows')->default(0);
            $table->unsignedInteger('cols')->default(0);
            $table->json('seat_map')->nullable();
            $table->json('position')->nullable();
            $table->string('color')->default('#3b82f6');
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_sections');
    }
};
