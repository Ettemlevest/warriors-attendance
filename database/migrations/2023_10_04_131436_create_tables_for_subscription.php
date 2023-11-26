<?php

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
        });

        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedSmallInteger('sessions')->default(0);
            $table->unsignedSmallInteger('expiration_in_days')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('name');
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Plan::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->dateTime('purchased_at');
            $table->dateTime('expired_at')->nullable();
            $table->timestamps();
        });

        Schema::table('trainings_attendance', function (Blueprint $table) {
            $table->foreignIdFor(Subscription::class)
                ->nullable()
                ->after('user_id')
                ->constrained()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('trainings_attendance', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(Subscription::class);
        });

        Schema::dropIfExists('subscriptions');

        Schema::dropIfExists('plans');
    }
};
