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
        Schema::create('receptionists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('national_id', 50)->unique();
            $table->string('phone_number', 20);
            $table->string('avatar_image')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('banned_at')->nullable();
            $table->rememberToken();
          
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