<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\TrainingAttendance
 *
 * @property int $id
 * @property int $training_id
 * @property int $user_id
 * @property bool $extra
 * @property bool $attended
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Training|null $training
 * @property-read \App\Models\User|null $user
 */
class TrainingAttendance extends Model
{
    use HasFactory;

    protected $table = 'trainings_attendance';

    protected $casts = [
        'extra' => 'boolean',
        'attended' => 'boolean',
    ];

    protected $fillable = [
        'training_id',
        'user_id',
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
