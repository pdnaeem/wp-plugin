<?php

namespace App\Http\Controllers;

use App\Models\Calculation;
use App\Models\EmissionFunction;
use App\Models\GeometryDataset;
use App\Models\Substance;
use App\Models\WeatherDataset;
use App\Models\BuildingMaterial;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index', [
            'counts' => [
                'geometry' => GeometryDataset::count(),
                'weather' => WeatherDataset::count(),
                'substances' => Substance::count(),
                'materials' => BuildingMaterial::count(),
                'emissions' => EmissionFunction::count(),
                'calculations' => Calculation::count(),
            ],
            'recentCalculations' => Calculation::latest()->take(5)->get(),
        ]);
    }
}
