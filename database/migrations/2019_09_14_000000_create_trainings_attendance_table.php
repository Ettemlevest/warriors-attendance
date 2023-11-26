<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainings_attendance', function (Blueprint $table) {
            $table->unsignedInteger('training_id');
            $table->foreign('training_id')->references('id')->on('trainings')->onDelete('cascade');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->primary(['training_id', 'user_id']);
            $table->boolean('extra')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainings_attendance');
    }
};
