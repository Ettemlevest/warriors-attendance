<?php

use App\Models\Training;
use App\Models\TrainingAttendance;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainings_attendance_new', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Training::class);
            $table->foreignIdFor(User::class);
            $table->boolean('extra')->default(false);
            $table->boolean('attended')->default(false);
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        TrainingAttendance::query()->get()->each(function (TrainingAttendance $attendance) {
            $attendance->replicate()
                ->forceFill([
                    'created_at' => $attendance->created_at,
                    'updated_at' => $attendance->updated_at,
                ])
                ->setTable('trainings_attendance_new')
                ->save();
        });

        Schema::rename('trainings_attendance', 'trainings_attendance_backup');
        Schema::rename('trainings_attendance_new', 'trainings_attendance');
    }

    public function down(): void
    {
        throw new LogicException("There's no way I'm writing a down method for this migration =]");
    }
};
