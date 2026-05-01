<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffSuggestion extends Model
{
    protected $fillable = [
        'user_id',
        'is_anonymous',
        'category',
        'title',
        'body',
        'status',
        'management_response',
        'responded_at',
        'responder_id',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'responded_at' => 'datetime',
    ];

    public const CATEGORIES = ['Operational', 'HR/People', 'Facilities', 'Process', 'Other'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responder_id');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasAnyRole(['admin', 'hr'])) {
            return $query;
        }

        return $query->where('is_anonymous', false);
    }
}
