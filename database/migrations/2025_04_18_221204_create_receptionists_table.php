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
        Schema::create('receptionists', function (Blueprint $table) {
            $table->id();
            $table->string('national_id', 50);
            $table->string('phone_number', 20);
            $table->string('avatar_image');
            $table->date('banned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receptionists');
    }
};
