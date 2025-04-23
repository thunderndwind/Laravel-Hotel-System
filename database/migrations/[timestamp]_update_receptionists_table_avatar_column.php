<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('receptionists', function (Blueprint $table) {
            $table->longText('avatar_image')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('receptionists', function (Blueprint $table) {
            $table->string('avatar_image')->nullable()->change();
        });
    }
};