<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    ];

    // Relationship with users
    public function users()
    {
        return $this->hasMany(User::class);
    }
    // Relationship with designations
    public function designations()
    {
        return $this->hasMany(Designation::class);
    }
}
