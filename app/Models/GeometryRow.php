<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeometryRow extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'geometry_dataset_id',
        'component_id',
        'surface_area_m2',
        'exposure_class',
        'material_subtype_code',
    ];

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(GeometryDataset::class, 'geometry_dataset_id');
    }
}
