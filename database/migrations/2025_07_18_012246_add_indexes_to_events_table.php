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
        Schema::table('events', function (Blueprint $table) {
            $table->index('type');
            $table->index('date');
            $table->index('description');
            // Composite index for type + date queries (most efficient for our ofType scope)
            $table->index(['type', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex(['events_type_index']);
            $table->dropIndex(['events_date_index']);
            $table->dropIndex(['events_description_index']);
            $table->dropIndex(['events_type_date_index']);
        });
    }
};
