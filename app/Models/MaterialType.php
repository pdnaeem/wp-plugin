<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialType extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function subtypes(): HasMany
    {
        return $this->hasMany(MaterialSubtype::class);
    }
}
