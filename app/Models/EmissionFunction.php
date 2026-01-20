<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmissionFunction extends Model
{
    protected $fillable = [
        'substance_id',
        'function_type',
        'parameters',
        'source',
        'diagnostics',
    ];

    protected $casts = [
        'parameters' => 'array',
        'diagnostics' => 'array',
    ];

    public function substance(): BelongsTo
    {
        return $this->belongsTo(Substance::class);
    }
}
