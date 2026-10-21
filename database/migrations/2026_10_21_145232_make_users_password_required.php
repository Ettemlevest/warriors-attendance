<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $usersWithoutPassword = User::withTrashed()->whereNull('password')->count();

        if ($usersWithoutPassword > 0) {
            throw new RuntimeException("{$usersWithoutPassword} user(s) have no password. Reset their passwords before making the column required.");
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable(false)->change();
        });
    }
};
