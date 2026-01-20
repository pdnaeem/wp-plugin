<?php

namespace App\Services\Model;

use App\Models\Calculation;
use App\Models\MaterialSubtype;
use Illuminate\Support\Facades\Storage;

class SimulationRunner
{
    public function run(Calculation $calculation): array
    {
        $settings = $calculation->settings ?? [];
        $wdrEnabled = (bool) ($settings['wdr']['enabled'] ?? false);
        $wdrFactor = (float) ($settings['wdr']['factor'] ?? 1.0);
        $waterClass = $settings['receiving_water_class'] ?? 'M';

        $geometryRows = $calculation->geometry->rows;
        $totalArea = $geometryRows->sum('surface_area_m2');
        $coefficients = $this->calculateRunoffCoefficient($geometryRows);

        $emissionFunction = $calculation->emissionFunction;
        $cumulativeRunoff = 0.0;
        $prevCumulativeEmission = 0.0;

        Storage::disk('public')->makeDirectory('calculations');
        $timeseriesPath = 'calculations/'.strtolower($calculation->id).'-timeseries.csv';
        $fullPath = Storage::disk('public')->path($timeseriesPath);
        $handle = fopen($fullPath, 'w');
        fputcsv($handle, [
            'timestamp',
            'precipitation_mm',
            'runoff_volume_l',
            'emission_mg',
            'concentration_mg_l',
            'cumulative_emission_mg_m2',
        ]);

        $totalEmissionMg = 0.0;
        $peakConcentration = 0.0;

        $query = $calculation->weather->rows()
            ->whereBetween('observed_at', [
                $settings['date_range']['start'],
                $settings['date_range']['end'],
            ])
            ->orderBy('observed_at');

        $query->chunkById(500, function ($rows) use (
            $handle,
            $wdrEnabled,
            $wdrFactor,
            $coefficients,
            $totalArea,
            $emissionFunction,
            &$cumulativeRunoff,
            &$prevCumulativeEmission,
            &$totalEmissionMg,
            &$peakConcentration
        ) {
            foreach ($rows as $row) {
                $precip = $row->precipitation_mm;
                $effectivePrecip = $wdrEnabled ? $precip * $wdrFactor : $precip;
                $runoffMm = $effectivePrecip * $coefficients['average'];
                $runoffVolumeL = $runoffMm * $totalArea;
                $cumulativeRunoff += $runoffMm;

                $cumulativeEmission = EmissionFunctionLibrary::evaluate(
                    $emissionFunction->function_type,
                    $cumulativeRunoff,
                    $emissionFunction->parameters ?? []
                );
                $incrementalEmission = max($cumulativeEmission - $prevCumulativeEmission, 0);
                $prevCumulativeEmission = $cumulativeEmission;
                $totalEmissionMg += $incrementalEmission * $totalArea;

                $concentration = $runoffVolumeL > 0
                    ? ($incrementalEmission * $totalArea) / $runoffVolumeL
                    : 0.0;

                $peakConcentration = max($peakConcentration, $concentration);

                fputcsv($handle, [
                    $row->observed_at->format('Y-m-d H:i:s'),
                    round($precip, 4),
                    round($runoffVolumeL, 4),
                    round($incrementalEmission * $totalArea, 6),
                    round($concentration, 6),
                    round($cumulativeEmission, 6),
                ]);
            }
        });

        fclose($handle);

        $dilutionFactor = match ($waterClass) {
            'S' => 10,
            'M' => 100,
            'L' => 1000,
            default => 100,
        };

        return [
            'summary' => [
                'total_area_m2' => $totalArea,
                'average_runoff_coefficient' => $coefficients['average'],
                'total_emission_mg' => $totalEmissionMg,
                'peak_concentration_mg_l' => $peakConcentration,
                'diluted_peak_concentration_mg_l' => $peakConcentration / $dilutionFactor,
                'receiving_water_class' => $waterClass,
            ],
            'timeseries_path' => $timeseriesPath,
        ];
    }

    private function calculateRunoffCoefficient($geometryRows): array
    {
        $areaTotal = 0.0;
        $weighted = 0.0;

        $coefficientsByCode = MaterialSubtype::query()
            ->get(['code', 'runoff_coefficient'])
            ->pluck('runoff_coefficient', 'code')
            ->toArray();

        foreach ($geometryRows as $row) {
            $areaTotal += $row->surface_area_m2;
            $coeff = $coefficientsByCode[$row->material_subtype_code] ?? 0.7;
            $weighted += $coeff * $row->surface_area_m2;
        }

        return [
            'average' => $areaTotal > 0 ? $weighted / $areaTotal : 0.7,
        ];
    }
}
