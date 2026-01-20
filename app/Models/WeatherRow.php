<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeatherRow extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'weather_dataset_id',
        'observed_at',
        'precipitation_mm',
        'temperature_c',
        'wind_speed_ms',
        'humidity_pct',
    ];

    protected $casts = [
        'observed_at' => 'datetime',
    ];

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(WeatherDataset::class, 'weather_dataset_id');
    }
}
