<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainings_attendance', function (Blueprint $table) {
            $table->boolean('attended')->default(false)->after('extra');
        });
    }

    public function down(): void
    {
        Schema::table('trainings_attendance', function (Blueprint $table) {
            $table->dropColumn('attended');
        });
    }
};
