<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Calculation extends Model
{
    protected $fillable = [
        'name',
        'description',
        'geometry_dataset_id',
        'weather_dataset_id',
        'substance_id',
        'emission_function_id',
        'status',
        'settings',
        'logs',
    ];

    protected $casts = [
        'settings' => 'array',
        'logs' => 'array',
    ];

    public function geometry(): BelongsTo
    {
        return $this->belongsTo(GeometryDataset::class, 'geometry_dataset_id');
    }

    public function weather(): BelongsTo
    {
        return $this->belongsTo(WeatherDataset::class, 'weather_dataset_id');
    }

    public function substance(): BelongsTo
    {
        return $this->belongsTo(Substance::class);
    }

    public function emissionFunction(): BelongsTo
    {
        return $this->belongsTo(EmissionFunction::class);
    }

    public function result(): HasOne
    {
        return $this->hasOne(CalculationResult::class);
    }

    public function job(): HasOne
    {
        return $this->hasOne(CalculationJob::class);
    }
}
