<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiRubricTemplate extends Model
{
    protected $fillable = ['level_id', 'name', 'version', 'is_active', 'effective_from'];

    protected $casts = ['is_active' => 'boolean', 'effective_from' => 'date'];

    public function level(): BelongsTo { return $this->belongsTo(Level::class); }

    public function items(): HasMany { return $this->hasMany(KpiRubricItem::class, 'template_id')->orderBy('order'); }
}
