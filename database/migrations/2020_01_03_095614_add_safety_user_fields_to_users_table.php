<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('safety_person')->nullable();
            $table->string('safety_phone')->nullable();
        });
    }

    public function down(): void
    {
        // need to separate dropColumn calls into multiple table modifications
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('safety_person');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('safety_phone');
        });
    }
};
