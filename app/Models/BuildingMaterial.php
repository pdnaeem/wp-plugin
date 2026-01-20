<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BuildingMaterial extends Model
{
    protected $fillable = [
        'material_subtype_id',
        'name',
        'description',
    ];

    public function subtype(): BelongsTo
    {
        return $this->belongsTo(MaterialSubtype::class, 'material_subtype_id');
    }

    public function substances(): BelongsToMany
    {
        return $this->belongsToMany(Substance::class)
            ->withPivot(['initial_content'])
            ->withTimestamps();
    }
}
