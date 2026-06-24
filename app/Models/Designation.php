<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Designation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'department_id',
    ];

    // Relationship with users
    public function users()
    {
        return $this->hasMany(User::class);
    }
    // Relationship with department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
