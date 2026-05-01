<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HandbookPart extends Model
{
    protected $fillable = ['code', 'title', 'tagline', 'intro_body', 'accent_color', 'icon', 'order', 'is_published'];

    public function sections(): HasMany
    {
        return $this->hasMany(ContentSection::class, 'part_id')
            ->where('category', 'handbook')
            ->where('is_published', true)
            ->orderBy('order');
    }
}
