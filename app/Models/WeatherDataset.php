<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeatherDataset extends Model
{
    protected $fillable = [
        'name',
        'description',
        'raw_path',
        'summary',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'summary' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function rows(): HasMany
    {
        return $this->hasMany(WeatherRow::class);
    }
}
