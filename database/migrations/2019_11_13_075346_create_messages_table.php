<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('body')->nullable();
            $table->timestamp('showed_from')->nullable();
            $table->timestamp('showed_to')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
};
