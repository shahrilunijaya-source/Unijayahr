<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'job_title',
        'email',
        'password',
        'ic_number',
        'phone',
        'level_id',
        'unit_id',
        'superior_id',
        'join_date',
        'status',
        'avatar_path',
        'google_id',
        'provider',
        'is_active',
        'must_change_password',
        'last_login_at',
        'work_start_time',
        'work_end_time',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'ic_number',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'last_login_at'        => 'datetime',
            'join_date'            => 'date',
            'password'             => 'hashed',
            'ic_number'            => 'encrypted',
            'is_active'            => 'boolean',
            'must_change_password' => 'boolean',
            'work_start_time'      => 'string',
            'work_end_time'        => 'string',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active;
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function superior(): BelongsTo
    {
        return $this->belongsTo(User::class, 'superior_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(User::class, 'superior_id');
    }

    public function employeeProfile(): HasOne
    {
        return $this->hasOne(EmployeeProfile::class);
    }

    public function bankDetail(): HasOne
    {
        return $this->hasOne(EmployeeBankDetail::class);
    }

    public function emergencyContacts(): HasMany
    {
        return $this->hasMany(EmployeeEmergencyContact::class);
    }

    public function education(): HasMany
    {
        return $this->hasMany(EmployeeEducation::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function leaveApprovals(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'manager_id');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(\App\Models\AttendanceRecord::class);
    }
}
