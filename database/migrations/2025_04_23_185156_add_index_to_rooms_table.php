<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Composite indexes with explicit names
            $table->index(['number', 'floor_id'], 'rooms_number_floor_index');
            $table->index(['manager_id', 'deleted_at'], 'rooms_manager_status_index');
            $table->index(['created_at', 'id'], 'rooms_sorting_index');
            $table->index(['price', 'capacity'], 'rooms_search_index');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropIndex('rooms_number_floor_index');
            $table->dropIndex('rooms_manager_status_index');
            $table->dropIndex('rooms_sorting_index');
            $table->dropIndex('rooms_search_index');
        });
    }
};
