<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PersonalitySection extends Model
{
    protected $fillable = ['assessment_id', 'title', 'description', 'order'];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(PersonalityAssessment::class, 'assessment_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(PersonalityQuestion::class, 'section_id')->orderBy('order');
    }
}
