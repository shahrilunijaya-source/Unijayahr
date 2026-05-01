<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalityQuestion extends Model
{
    protected $fillable = ['section_id', 'prompt', 'type', 'options', 'scoring_weights', 'order'];

    protected $casts = ['options' => 'array', 'scoring_weights' => 'array'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(PersonalitySection::class, 'section_id');
    }
}
