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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('number')->unique(); // could be the PK
            $table->unsignedInteger('price');
            $table->unsignedInteger('capacity');
            $table->foreignId('manager_id')->constrained('base_users')->cascadeOnDelete();
            $table->foreignId('floor_id')->constrained('floors')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
