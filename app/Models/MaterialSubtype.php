<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialSubtype extends Model
{
    protected $fillable = [
        'material_type_id',
        'code',
        'runoff_coefficient',
        'description',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(MaterialType::class, 'material_type_id');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(BuildingMaterial::class);
    }
}
