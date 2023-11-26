<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('place');
            $table->datetime('start_at');
            $table->unsignedSmallInteger('length')->default(60);
            $table->unsignedSmallInteger('max_attendees');
            $table->boolean('can_attend_more')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
