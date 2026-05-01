<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDocument extends Model
{
    const CATEGORIES = [
        'ic_copy'            => 'IC Copy',
        'offer_letter'       => 'Offer Letter',
        'contract'           => 'Contract',
        'cert'               => 'Certificate',
        'medical_cert'       => 'Medical Certificate',
        'resignation_letter' => 'Resignation Letter',
        'other'              => 'Other',
    ];

    protected $fillable = [
        'user_id', 'category', 'label', 'file_path',
        'file_size', 'mime_type', 'uploaded_by',
    ];

    protected function casts(): array
    {
        return ['file_size' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getDisplayLabelAttribute(): string
    {
        return $this->category === 'other'
            ? ($this->label ?: 'Other')
            : (self::CATEGORIES[$this->category] ?? $this->category);
    }
}
