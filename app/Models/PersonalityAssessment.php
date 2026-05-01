<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PersonalityAssessment extends Model
{
    protected $fillable = ['name', 'description', 'scoring_config', 'is_active'];

    protected $casts = ['scoring_config' => 'array', 'is_active' => 'boolean'];

    public function sections(): HasMany
    {
        return $this->hasMany(PersonalitySection::class, 'assessment_id')->orderBy('order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(PersonalityResponse::class, 'assessment_id');
    }
}
