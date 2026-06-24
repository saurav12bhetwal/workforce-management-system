<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'attendance_date',
        'check_in',
        'check_in_lat',
        'check_in_lng',
        'check_in_location',
        'check_out',
        'check_out_lat',
        'check_out_lng',
        'check_out_location',
        'working_minutes',
        'status',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'working_minutes' => 'integer',
    ];

    // Scopes
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('attendance_date', $date);
    }

    public function scopeForMonth($query, $month, $year)
    {
        return $query->whereMonth('attendance_date', $month)
                     ->whereYear('attendance_date', $year);
    }

    public function scopePresent($query)
    {
        return $query->whereNotNull('check_in');
    }

    public function scopeCheckedIn($query)
    {
        return $query->whereNotNull('check_in')->whereNull('check_out');
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('check_in')->whereNotNull('check_out');
    }

    // Accessors
    public function isCheckedIn(): bool
    {
        return !is_null($this->check_in) && is_null($this->check_out);
    }

    public function isCompleted(): bool
    {
        return !is_null($this->check_in) && !is_null($this->check_out);
    }

    public function workingHours(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->working_minutes) {
                    $hours = floor($this->working_minutes / 60);
                    $minutes = $this->working_minutes % 60;
                    return sprintf('%02d:%02d:00', $hours, $minutes);
                }
                
                // If currently checked in, calculate live
                if ($this->isCheckedIn()) {
                    $elapsed = $this->check_in->diffInMinutes(now());
                    $hours = floor($elapsed / 60);
                    $minutes = $elapsed % 60;
                    return sprintf('%02d:%02d:00', $hours, $minutes);
                }
                
                return '00:00:00';
            }
        );
    }

    public function workingHoursFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                $minutes = $this->working_minutes ?? 0;
                $hours = floor($minutes / 60);
                $mins = $minutes % 60;
                return "{$hours}h {$mins}m";
            }
        );
    }

    public function checkInTimeFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->check_in ? $this->check_in->format('h:i A') : null
        );
    }

    public function checkOutTimeFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->check_out ? $this->check_out->format('h:i A') : null
        );
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}