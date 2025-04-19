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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('avatar_image');
            $table->string('phone_number', 20);
            $table->enum('gender', ['male', 'female']);
            $table->nullableMorphs('approver'); // type and id
            $table->timestamp('approved_at')->nullable();
            $table->string('country', 30);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
