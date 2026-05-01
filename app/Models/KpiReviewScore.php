<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiReviewScore extends Model
{
    protected $fillable = [
        'review_id', 'item_id', 'item_label_snapshot', 'item_weight_snapshot', 'max_score_snapshot',
        'self_score', 'self_comment', 'superior_score', 'superior_comment',
    ];

    public function review(): BelongsTo { return $this->belongsTo(KpiReview::class, 'review_id'); }
    public function item(): BelongsTo   { return $this->belongsTo(KpiRubricItem::class, 'item_id'); }
}
