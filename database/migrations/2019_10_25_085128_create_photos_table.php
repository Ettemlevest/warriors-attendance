<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('album_id')->nullable();
            $table->foreign('album_id')->references('id')->on('albums')->onDelete('cascade');
            $table->string('path');
            $table->timestamps();
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->foreign('cover_photo_id')->references('id')->on('photos');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
