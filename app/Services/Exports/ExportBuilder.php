<?php

namespace App\Services\Exports;

use App\Models\Calculation;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ExportBuilder
{
    public function build(Calculation $calculation, array $summary, string $timeseriesPath): string
    {
        Storage::disk('public')->makeDirectory('exports');
        $zipPath = 'exports/calculation-'.$calculation->id.'.zip';
        $fullZipPath = Storage::disk('public')->path($zipPath);

        $zip = new ZipArchive();
        $zip->open($fullZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $summaryCsv = $this->buildSummaryCsv($summary);
        $zip->addFromString('summary.csv', $summaryCsv);

        $timeseriesFullPath = Storage::disk('public')->path($timeseriesPath);
        $zip->addFile($timeseriesFullPath, 'timeseries.csv');

        $zip->addFromString('codebook.txt', $this->codebook());
        $zip->close();

        return $zipPath;
    }

    private function buildSummaryCsv(array $summary): string
    {
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, array_keys($summary));
        fputcsv($handle, array_values($summary));
        rewind($handle);
        $contents = stream_get_contents($handle);
        fclose($handle);

        return $contents;
    }

    private function codebook(): string
    {
        return <<<TXT
Timeseries columns:
- timestamp: observation timestamp (YYYY-MM-DD HH:MM:SS)
- precipitation_mm: hourly precipitation (mm)
- runoff_volume_l: runoff volume (L)
- emission_mg: emitted mass for timestep (mg)
- concentration_mg_l: concentration in runoff (mg/L)
- cumulative_emission_mg_m2: cumulative emission (mg/m^2)

Summary columns:
- total_area_m2
- average_runoff_coefficient
- total_emission_mg
- peak_concentration_mg_l
- diluted_peak_concentration_mg_l
- receiving_water_class
TXT;
    }
}
