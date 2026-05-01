<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentSection extends Model
{
    protected $fillable = [
        'category', 'title', 'slug', 'body', 'order',
        'is_published', 'last_updated_by', 'part_id',
    ];

    public function part(): BelongsTo
    {
        return $this->belongsTo(HandbookPart::class, 'part_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
