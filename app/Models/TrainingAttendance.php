<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingAttendance extends Model
{
    use HasFactory;

    protected $table = 'trainings_attendance';

    protected $casts = [
        'extra' => 'boolean',
        'attended' => 'boolean',
    ];

    protected $fillable = [
        'extra',
        'attended',
        'comment',
    ];

    protected $with = [
        'training',
        'user',
    ];

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
