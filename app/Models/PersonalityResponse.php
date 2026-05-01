<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PersonalityResponse extends Model
{
    protected $fillable = ['user_id', 'assessment_id', 'submitted_at', 'raw_scores', 'computed_result'];

    protected $casts = [
        'submitted_at'    => 'datetime',
        'raw_scores'      => 'array',
        'computed_result' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(PersonalityAssessment::class, 'assessment_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(PersonalityAnswer::class, 'response_id');
    }
}
