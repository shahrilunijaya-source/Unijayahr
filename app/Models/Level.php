<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Level extends Model
{
    protected $fillable = ['code', 'order', 'name', 'salary_min', 'salary_mid', 'salary_max', 'description'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function __toString(): string
    {
        return "{$this->code} — {$this->name}";
    }
}
