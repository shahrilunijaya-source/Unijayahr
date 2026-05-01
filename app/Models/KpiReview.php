<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiReview extends Model
{
    protected $fillable = [
        'user_id', 'reviewer_id', 'template_id', 'period_id',
        'status', 'pending_role',
        'self_total', 'superior_total', 'final_total',
        'self_submitted_at', 'superior_submitted_at', 'finalized_at',
    ];

    protected $casts = [
        'self_submitted_at'     => 'datetime',
        'superior_submitted_at' => 'datetime',
        'finalized_at'          => 'datetime',
    ];

    public function user(): BelongsTo     { return $this->belongsTo(User::class, 'user_id'); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewer_id'); }
    public function template(): BelongsTo { return $this->belongsTo(KpiRubricTemplate::class, 'template_id'); }
    public function period(): BelongsTo   { return $this->belongsTo(KpiPeriod::class, 'period_id'); }
    public function scores(): HasMany     { return $this->hasMany(KpiReviewScore::class, 'review_id'); }
}
