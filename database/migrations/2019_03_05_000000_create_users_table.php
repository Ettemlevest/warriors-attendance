<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email', 50)->unique();
            $table->string('password')->nullable();
            $table->boolean('owner')->default(false);
            $table->string('photo_path', 100)->nullable();
            $table->string('size', 3)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
