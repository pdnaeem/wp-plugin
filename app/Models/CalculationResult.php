<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalculationResult extends Model
{
    protected $fillable = [
        'calculation_id',
        'summary',
        'timeseries_path',
        'report_path',
        'export_path',
    ];

    protected $casts = [
        'summary' => 'array',
    ];

    public function calculation(): BelongsTo
    {
        return $this->belongsTo(Calculation::class);
    }
}
