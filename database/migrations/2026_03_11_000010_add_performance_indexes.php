<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->index('start_at');
            $table->index('category');
            $table->index('slug');
        });

        Schema::table('venues', function (Blueprint $table) {
            $table->index('city');
        });

        Schema::table('event_favorites', function (Blueprint $table) {
            $table->index(['user_id', 'event_id']);
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex(['start_at']);
            $table->dropIndex(['category']);
            $table->dropIndex(['slug']);
        });

        Schema::table('venues', function (Blueprint $table) {
            $table->dropIndex(['city']);
        });

        Schema::table('event_favorites', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'event_id']);
        });
    }
};
