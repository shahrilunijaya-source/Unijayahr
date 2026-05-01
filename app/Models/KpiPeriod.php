<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiPeriod extends Model
{
    protected $fillable = [
        'label', 'start_date', 'end_date', 'status',
        'self_weight', 'superior_weight', 'opened_at', 'closed_at',
    ];

    protected $casts = [
        'start_date' => 'date', 'end_date' => 'date',
        'opened_at'  => 'datetime', 'closed_at' => 'datetime',
    ];

    public function reviews(): HasMany { return $this->hasMany(KpiReview::class, 'period_id'); }
}
