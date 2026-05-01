<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalityAnswer extends Model
{
    protected $fillable = ['response_id', 'question_id', 'value'];

    protected $casts = ['value' => 'array'];

    public function response(): BelongsTo
    {
        return $this->belongsTo(PersonalityResponse::class, 'response_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(PersonalityQuestion::class, 'question_id');
    }
}
