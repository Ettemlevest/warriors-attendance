<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Artisan::call('attendance:prune-orphans');
        echo Artisan::output();

        Schema::table('trainings', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
        });

        Schema::table('trainings_attendance', function (Blueprint $table) {
            $table->foreign('training_id')->references('id')->on('trainings')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::dropIfExists('trainings_attendance_backup');
    }
};
