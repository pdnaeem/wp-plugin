<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeometryDataset extends Model
{
    protected $fillable = [
        'name',
        'description',
        'raw_path',
        'summary',
    ];

    protected $casts = [
        'summary' => 'array',
    ];

    public function rows(): HasMany
    {
        return $this->hasMany(GeometryRow::class);
    }
}
