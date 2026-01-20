<?php

namespace Database\Seeders;

use App\Models\MaterialSubtype;
use App\Models\MaterialType;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $roof = MaterialType::create([
            'name' => 'Roofing',
            'description' => 'Common roofing materials',
        ]);

        $facade = MaterialType::create([
            'name' => 'Facade',
            'description' => 'Facade and cladding materials',
        ]);

        MaterialSubtype::create([
            'material_type_id' => $roof->id,
            'code' => 'ROOF-METAL',
            'runoff_coefficient' => 0.9,
            'description' => 'Metal roof panels',
        ]);

        MaterialSubtype::create([
            'material_type_id' => $roof->id,
            'code' => 'ROOF-TILE',
            'runoff_coefficient' => 0.8,
            'description' => 'Ceramic tile roofing',
        ]);

        MaterialSubtype::create([
            'material_type_id' => $facade->id,
            'code' => 'FACADE-CLAD',
            'runoff_coefficient' => 0.6,
            'description' => 'Composite facade cladding',
        ]);
    }
}
