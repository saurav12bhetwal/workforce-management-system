<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_code',
        'phone',
        'address',
        'status',
        'department_id',
        'designation_id',
        'manager_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    // Accessor for role name
    public function getRoleNameAttribute()
    {
        return $this->roles->first()->name ?? 'No Role';
    }

    // Boot method to auto-generate employee code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->employee_code)) {
                $lastUser = static::withTrashed()->orderBy('id', 'desc')->first();
                $nextId = $lastUser ? $lastUser->id + 1 : 1;
                $user->employee_code = 'EMP-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}