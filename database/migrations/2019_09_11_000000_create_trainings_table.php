<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('place');
            $table->datetime('start_at');
            $table->unsignedSmallInteger('length')->default(60);
            $table->unsignedSmallInteger('max_attendees');
            $table->timestamps();
        });
    }
}
