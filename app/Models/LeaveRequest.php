<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type',
        'from_date',
        'to_date',
        'reason',
        'status',
        'approved_by',
        'rejection_reason',
        'approved_at',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // Constants
    const TYPES = [
        'Casual Leave',
        'Sick Leave',
        'Half Day',
        'Early Leave',
        'Work From Home',
        'Field Visit'
    ];

    const STATUSES = [
        'Pending',
        'Approved',
        'Rejected'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'Rejected');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForMonth($query, $month, $year)
    {
        return $query->whereMonth('from_date', $month)
                     ->whereYear('from_date', $year);
    }

    // Accessors
    public function getDaysCountAttribute()
    {
        return $this->from_date->diffInDays($this->to_date) + 1;
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'Pending' => 'warning',
            'Approved' => 'success',
            'Rejected' => 'danger',
            default => 'secondary'
        };
    }

    public function getStatusIconAttribute()
    {
        return match($this->status) {
            'Pending' => 'fa-clock',
            'Approved' => 'fa-check-circle',
            'Rejected' => 'fa-times-circle',
            default => 'fa-circle'
        };
    }

    public function getLeaveTypeBadgeClassAttribute()
    {
        return match($this->leave_type) {
            'Casual Leave' => 'info',
            'Sick Leave' => 'danger',
            'Half Day' => 'warning',
            'Early Leave' => 'primary',
            'Work From Home' => 'success',
            'Field Visit' => 'secondary',
            default => 'secondary'
        };
    }

    // Check overlapping leaves
    public static function isOverlapping($userId, $fromDate, $toDate, $excludeId = null)
    {
        $query = self::where('user_id', $userId)
            ->where(function($q) use ($fromDate, $toDate) {
                $q->whereBetween('from_date', [$fromDate, $toDate])
                  ->orWhereBetween('to_date', [$fromDate, $toDate])
                  ->orWhere(function($q2) use ($fromDate, $toDate) {
                      $q2->where('from_date', '<=', $fromDate)
                         ->where('to_date', '>=', $toDate);
                  });
            })
            ->whereIn('status', ['Pending', 'Approved']);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    // Check if user has enough leave balance (optional)
    public static function hasBalance($userId, $leaveType, $days)
    {
        // This is a placeholder - implement your leave balance logic here
        // For now, return true (unlimited leaves)
        return true;
    }
}