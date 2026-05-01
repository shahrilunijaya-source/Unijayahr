<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiRubricItem extends Model
{
    protected $fillable = ['template_id', 'category', 'criterion', 'description', 'weight', 'max_score', 'order'];

    public function template(): BelongsTo { return $this->belongsTo(KpiRubricTemplate::class, 'template_id'); }
}
