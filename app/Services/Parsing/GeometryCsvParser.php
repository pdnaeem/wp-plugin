<?php

namespace App\Services\Parsing;

use App\Models\GeometryDataset;
use Illuminate\Support\Facades\DB;

class GeometryCsvParser
{
    public function parse(string $path): array
    {
        $handle = fopen($path, 'r');
        if (!$handle) {
            throw new \RuntimeException('Unable to open geometry CSV.');
        }

        $rows = [];
        $totalArea = 0.0;
        $exposureDistribution = [];
        $count = 0;

        while (($line = fgetcsv($handle, 0, ';')) !== false) {
            if ($this->isHeader($line)) {
                continue;
            }

            [$componentId, $area, $exposure, $materialCode] = array_pad($line, 4, null);
            $areaValue = (float) str_replace(',', '.', (string) $area);
            $exposureKey = trim((string) $exposure);

            if ($areaValue <= 0) {
                continue;
            }

            $rows[] = [
                'component_id' => trim((string) $componentId),
                'surface_area_m2' => $areaValue,
                'exposure_class' => $exposureKey,
                'material_subtype_code' => trim((string) $materialCode),
            ];

            $totalArea += $areaValue;
            $exposureDistribution[$exposureKey] = ($exposureDistribution[$exposureKey] ?? 0) + $areaValue;
            $count++;
        }

        fclose($handle);

        return [
            'rows' => $rows,
            'summary' => [
                'component_count' => $count,
                'total_surface_area_m2' => $totalArea,
                'exposure_distribution_m2' => $exposureDistribution,
            ],
        ];
    }

    public function ingest(GeometryDataset $dataset, string $path): array
    {
        $handle = fopen($path, 'r');
        if (!$handle) {
            throw new \RuntimeException('Unable to open geometry CSV.');
        }

        $totalArea = 0.0;
        $exposureDistribution = [];
        $count = 0;
        $batch = [];

        while (($line = fgetcsv($handle, 0, ';')) !== false) {
            if ($this->isHeader($line)) {
                continue;
            }

            [$componentId, $area, $exposure, $materialCode] = array_pad($line, 4, null);
            $areaValue = (float) str_replace(',', '.', (string) $area);
            $exposureKey = trim((string) $exposure);

            if ($areaValue <= 0) {
                continue;
            }

            $batch[] = [
                'geometry_dataset_id' => $dataset->id,
                'component_id' => trim((string) $componentId),
                'surface_area_m2' => $areaValue,
                'exposure_class' => $exposureKey,
                'material_subtype_code' => trim((string) $materialCode),
            ];

            $totalArea += $areaValue;
            $exposureDistribution[$exposureKey] = ($exposureDistribution[$exposureKey] ?? 0) + $areaValue;
            $count++;

            if (count($batch) >= 500) {
                DB::table('geometry_rows')->insert($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table('geometry_rows')->insert($batch);
        }

        fclose($handle);

        return [
            'summary' => [
                'component_count' => $count,
                'total_surface_area_m2' => $totalArea,
                'exposure_distribution_m2' => $exposureDistribution,
            ],
        ];
    }

    private function isHeader(array $line): bool
    {
        $value = strtolower(trim($line[0] ?? ''));
        return str_contains($value, 'component');
    }
}
