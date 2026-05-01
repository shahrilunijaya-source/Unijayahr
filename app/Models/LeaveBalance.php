<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    protected $fillable = [
        'user_id', 'leave_type_id', 'year',
        'allocated_days', 'carried_over',
    ];

    protected function casts(): array
    {
        return [
            'allocated_days' => 'decimal:1',
            'carried_over'   => 'decimal:1',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function totalAllocated(): float
    {
        return (float) $this->allocated_days + (float) $this->carried_over;
    }

    public function usedDays(): float
    {
        return (float) LeaveRequest::where('user_id', $this->user_id)
            ->where('leave_type_id', $this->leave_type_id)
            ->where('status', 'approved')
            ->whereBetween('start_date', ["{$this->year}-01-01", "{$this->year}-12-31"])
            ->sum('total_days');
    }

    public function remainingDays(): float
    {
        return max(0, $this->totalAllocated() - $this->usedDays());
    }
}
