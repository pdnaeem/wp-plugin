<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Substance extends Model
{
    protected $fillable = [
        'name',
        'cas_number',
        'ec_number',
        'acute_threshold',
        'chronic_threshold',
        'description',
    ];

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(BuildingMaterial::class)
            ->withPivot(['initial_content'])
            ->withTimestamps();
    }

    public function emissionFunctions(): HasMany
    {
        return $this->hasMany(EmissionFunction::class);
    }
}
