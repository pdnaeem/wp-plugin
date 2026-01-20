<?php

namespace App\Http\Controllers;

use App\Models\BuildingMaterial;
use App\Models\MaterialSubtype;
use App\Models\MaterialType;
use App\Models\Substance;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        return view('materials.index', [
            'types' => MaterialType::with('subtypes')->get(),
            'materials' => BuildingMaterial::with('subtype.type')->latest()->paginate(20),
            'substances' => Substance::all(),
        ]);
    }

    public function storeType(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        MaterialType::create($data);

        return redirect()->route('materials.index');
    }

    public function storeSubtype(Request $request)
    {
        $data = $request->validate([
            'material_type_id' => ['required', 'exists:material_types,id'],
            'code' => ['required', 'string', 'max:50'],
            'runoff_coefficient' => ['required', 'numeric', 'min:0', 'max:1'],
            'description' => ['nullable', 'string'],
        ]);

        MaterialSubtype::create($data);

        return redirect()->route('materials.index');
    }

    public function storeMaterial(Request $request)
    {
        $data = $request->validate([
            'material_subtype_id' => ['required', 'exists:material_subtypes,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'substances' => ['array'],
            'substances.*.id' => ['exists:substances,id'],
            'substances.*.initial_content' => ['numeric'],
        ]);

        $material = BuildingMaterial::create([
            'material_subtype_id' => $data['material_subtype_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        if (!empty($data['substances'])) {
            $attach = [];
            foreach ($data['substances'] as $substance) {
                if (!empty($substance['id'])) {
                    $attach[$substance['id']] = ['initial_content' => $substance['initial_content'] ?? 0];
                }
            }
            $material->substances()->sync($attach);
        }

        return redirect()->route('materials.index');
    }
}
