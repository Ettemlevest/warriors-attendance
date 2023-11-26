<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->text('description')->nullable();
        });

        Schema::table('trainings_attendance', function (Blueprint $table) {
            $table->text('comment')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('trainings_attendance', function (Blueprint $table) {
            $table->dropColumn('comment');
        });
    }
};
