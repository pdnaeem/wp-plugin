<?php

namespace App\Http\Controllers;

use App\Models\Calculation;
use App\Models\CalculationJob;
use App\Models\EmissionFunction;
use App\Models\GeometryDataset;
use App\Models\Substance;
use App\Models\WeatherDataset;
use Illuminate\Http\Request;

class CalculationController extends Controller
{
    public function index()
    {
        return view('calculations.index', [
            'calculations' => Calculation::latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return view('calculations.create', [
            'geometry' => GeometryDataset::all(),
            'weather' => WeatherDataset::all(),
            'substances' => Substance::all(),
            'emissionFunctions' => EmissionFunction::with('substance')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'geometry_dataset_id' => ['required', 'exists:geometry_datasets,id'],
            'weather_dataset_id' => ['required', 'exists:weather_datasets,id'],
            'substance_id' => ['required', 'exists:substances,id'],
            'emission_function_id' => ['required', 'exists:emission_functions,id'],
            'date_range_start' => ['required', 'date'],
            'date_range_end' => ['required', 'date'],
            'wdr_enabled' => ['nullable', 'boolean'],
            'wdr_factor' => ['nullable', 'numeric'],
            'receiving_water_class' => ['nullable', 'in:S,M,L'],
        ]);

        $calculation = Calculation::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'geometry_dataset_id' => $data['geometry_dataset_id'],
            'weather_dataset_id' => $data['weather_dataset_id'],
            'substance_id' => $data['substance_id'],
            'emission_function_id' => $data['emission_function_id'],
            'status' => 'queued',
            'settings' => [
                'date_range' => [
                    'start' => $data['date_range_start'],
                    'end' => $data['date_range_end'],
                ],
                'wdr' => [
                    'enabled' => (bool) ($data['wdr_enabled'] ?? false),
                    'factor' => (float) ($data['wdr_factor'] ?? 1.0),
                ],
                'receiving_water_class' => $data['receiving_water_class'] ?? 'M',
            ],
            'logs' => [],
        ]);

        CalculationJob::create([
            'calculation_id' => $calculation->id,
            'status' => 'queued',
            'attempts' => 0,
        ]);

        return redirect()->route('calculations.show', $calculation);
    }

    public function show(Calculation $calculation)
    {
        return view('calculations.show', [
            'calculation' => $calculation->load('result', 'geometry', 'weather', 'substance', 'emissionFunction'),
        ]);
    }
}
