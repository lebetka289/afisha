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
        Schema::create('event_seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_section_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->unsignedInteger('row_number')->default(0);
            $table->unsignedInteger('col_number')->default(0);
            $table->string('status')->default('available');
            $table->decimal('price', 10, 2)->default(0);
            $table->json('position')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['event_section_id', 'row_number', 'col_number'], 'event_seats_section_row_col_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_seats');
    }
};
