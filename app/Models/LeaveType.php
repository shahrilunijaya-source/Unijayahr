<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    protected $fillable = [
        'name', 'code', 'is_paid', 'requires_document',
        'max_days_per_year', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_paid'           => 'boolean',
            'requires_document' => 'boolean',
            'is_active'         => 'boolean',
            'max_days_per_year' => 'decimal:1',
        ];
    }

    public function balances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public static function active(): \Illuminate\Database\Eloquent\Builder
    {
        return static::where('is_active', true);
    }
}
