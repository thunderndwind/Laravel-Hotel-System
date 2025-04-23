<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('floors', function (Blueprint $table) {
            $table->index('number');
            $table->index('deleted_at');
            // Composite indexes with explicit names for better maintenance
            $table->index(['name', 'number'], 'floors_name_number_index');
            $table->index(['manager_id', 'deleted_at'], 'floors_manager_status_index');
            $table->index(['created_at', 'id'], 'floors_sorting_index');
        });

        Schema::table('users', function (Blueprint $table) {
            // Index for user name searches
            $table->index('name', 'users_name_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_name_index');
        });

        Schema::table('floors', function (Blueprint $table) {
            // Drop single column indexes
            $table->dropIndex('floors_name_index');
            $table->dropIndex('floors_number_index');
            $table->dropIndex('floors_manager_id_index');
            $table->dropIndex('floors_deleted_at_index');
            $table->dropIndex('floors_created_at_index');

            // Drop composite indexes
            $table->dropIndex('floors_name_number_index');
            $table->dropIndex('floors_manager_status_index');
            $table->dropIndex('floors_sorting_index');
        });
    }
};
