<?php

namespace App;

use League\Glide\Server;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

final class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use SoftDeletes, Authenticatable, Authorizable;

    protected $dates = [
        'birth_date',
    ];

    protected $casts = [
        'owner' => 'boolean',
    ];

    protected $appends = [
        'age',
    ];

    public function getOwnerAttribute()
    {
        return $this->attributes['owner'] === '1';
    }

    public function getAgeAttribute()
    {
        if (is_null($this->attributes['birth_date'])) {
            return null;
        }

        return $this->birth_date->age;
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    public function photoUrl(array $attributes)
    {
        if ($this->photo_path) {
            return URL::to(App::make(Server::class)->fromPath($this->photo_path, $attributes));
        }
    }

    public function scopeOrderByName($query)
    {
        $query->orderBy('name');
    }

    public function scopeWhereRole($query, $role)
    {
        switch ($role) {
            case 'user': return $query->where('owner', false);
            case 'owner': return $query->where('owner', true);
        }
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        })->when($filters['role'] ?? null, function ($query, $role) {
            $query->whereRole($role);
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }

    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'trainings_attendance')->withTimestamps();
    }

    public function last_attendance_date()
    {
        if ($this->trainings->count() == 0) {
            return false;
        }

        return $this->trainings()
                    ->orderBy('trainings_attendance.created_at', 'desc')
                    ->first()->pivot->created_at;
    }
}
