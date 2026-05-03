<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'date',
        'clock_in_at', 'clock_in_lat', 'clock_in_lng', 'clock_in_accuracy', 'clock_in_photo_path',
        'clock_out_at', 'clock_out_lat', 'clock_out_lng', 'clock_out_accuracy', 'clock_out_photo_path',
        'is_late', 'late_minutes', 'total_hours',
        'note', 'corrected_by',
    ];

    protected function casts(): array
    {
        return [
            'date'               => 'date',
            'clock_in_at'        => 'datetime',
            'clock_out_at'       => 'datetime',
            'is_late'            => 'boolean',
            'total_hours'        => 'decimal:2',
            'clock_in_lat'       => 'decimal:7',
            'clock_in_lng'       => 'decimal:7',
            'clock_in_accuracy'  => 'decimal:2',
            'clock_out_lat'      => 'decimal:7',
            'clock_out_lng'      => 'decimal:7',
            'clock_out_accuracy' => 'decimal:2',
        ];
    }

    // Relations

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function correctedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }

    // Scopes

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForDate(Builder $query, mixed $date): Builder
    {
        return $query->whereDate('date', $date);
    }

    public function scopeBetween(Builder $query, mixed $from, mixed $to): Builder
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    public function scopeLate(Builder $query): Builder
    {
        return $query->where('is_late', true);
    }

    public function scopeIncomplete(Builder $query): Builder
    {
        return $query->whereNotNull('clock_in_at')->whereNull('clock_out_at');
    }

    // Helpers

    public function isClockedIn(): bool
    {
        return $this->clock_in_at !== null;
    }

    public function isComplete(): bool
    {
        return $this->clock_in_at !== null && $this->clock_out_at !== null;
    }

    public function statusLabel(): string
    {
        if ($this->clock_in_at === null) return 'Absent';
        if ($this->clock_out_at === null) return 'Incomplete';
        return $this->is_late ? 'Late' : 'Present';
    }

    public function clockInMapUrl(): ?string
    {
        if ($this->clock_in_lat === null || $this->clock_in_lng === null) return null;
        return "https://maps.google.com/?q={$this->clock_in_lat},{$this->clock_in_lng}";
    }

    public function clockOutMapUrl(): ?string
    {
        if ($this->clock_out_lat === null || $this->clock_out_lng === null) return null;
        return "https://maps.google.com/?q={$this->clock_out_lat},{$this->clock_out_lng}";
    }
}
