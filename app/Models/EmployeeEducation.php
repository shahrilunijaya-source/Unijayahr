<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeEducation extends Model
{
    protected $fillable = ['user_id', 'institution', 'qualification', 'field_of_study', 'year_completed'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
