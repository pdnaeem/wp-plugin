<?php

namespace App\Services\Parsing;

use App\Models\WeatherDataset;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

class WeatherCsvParser
{
    public function parse(string $path): array
    {
        $handle = fopen($path, 'r');
        if (!$handle) {
            throw new \RuntimeException('Unable to open weather CSV.');
        }

        $rows = [];
        $count = 0;
        $missing = 0;
        $totalPrecip = 0.0;
        $startsAt = null;
        $endsAt = null;

        while (($line = fgetcsv($handle, 0, ';')) !== false) {
            if ($this->isHeader($line)) {
                continue;
            }

            [$timestamp, $precip, $temp, $wind, $humidity] = array_pad($line, 5, null);
            $date = $this->parseDate($timestamp);
            if (!$date) {
                $missing++;
                continue;
            }

            $precipValue = $this->parseFloat($precip);
            $tempValue = $this->parseFloat($temp);
            $windValue = $this->parseFloat($wind);
            $humidityValue = $this->parseFloat($humidity);

            $rows[] = [
                'observed_at' => $date->format('Y-m-d H:i:s'),
                'precipitation_mm' => $precipValue,
                'temperature_c' => $tempValue,
                'wind_speed_ms' => $windValue,
                'humidity_pct' => $humidityValue,
            ];

            $count++;
            $totalPrecip += $precipValue;
            $startsAt = $startsAt ? min($startsAt, $date) : $date;
            $endsAt = $endsAt ? max($endsAt, $date) : $date;
        }

        fclose($handle);

        return [
            'rows' => $rows,
            'summary' => [
                'row_count' => $count,
                'missing_rows' => $missing,
                'total_precipitation_mm' => $totalPrecip,
            ],
            'starts_at' => $startsAt?->format('Y-m-d H:i:s'),
            'ends_at' => $endsAt?->format('Y-m-d H:i:s'),
        ];
    }

    public function ingest(WeatherDataset $dataset, string $path): array
    {
        $handle = fopen($path, 'r');
        if (!$handle) {
            throw new \RuntimeException('Unable to open weather CSV.');
        }

        $count = 0;
        $missing = 0;
        $totalPrecip = 0.0;
        $startsAt = null;
        $endsAt = null;
        $batch = [];

        while (($line = fgetcsv($handle, 0, ';')) !== false) {
            if ($this->isHeader($line)) {
                continue;
            }

            [$timestamp, $precip, $temp, $wind, $humidity] = array_pad($line, 5, null);
            $date = $this->parseDate($timestamp);
            if (!$date) {
                $missing++;
                continue;
            }

            $precipValue = $this->parseFloat($precip);
            $tempValue = $this->parseFloat($temp);
            $windValue = $this->parseFloat($wind);
            $humidityValue = $this->parseFloat($humidity);

            $batch[] = [
                'weather_dataset_id' => $dataset->id,
                'observed_at' => $date->format('Y-m-d H:i:s'),
                'precipitation_mm' => $precipValue,
                'temperature_c' => $tempValue,
                'wind_speed_ms' => $windValue,
                'humidity_pct' => $humidityValue,
            ];

            $count++;
            $totalPrecip += $precipValue;
            $startsAt = $startsAt ? min($startsAt, $date) : $date;
            $endsAt = $endsAt ? max($endsAt, $date) : $date;

            if (count($batch) >= 500) {
                DB::table('weather_rows')->insert($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table('weather_rows')->insert($batch);
        }

        fclose($handle);

        return [
            'summary' => [
                'row_count' => $count,
                'missing_rows' => $missing,
                'total_precipitation_mm' => $totalPrecip,
            ],
            'starts_at' => $startsAt?->format('Y-m-d H:i:s'),
            'ends_at' => $endsAt?->format('Y-m-d H:i:s'),
        ];
    }

    private function parseDate(?string $value): ?DateTimeImmutable
    {
        if (!$value) {
            return null;
        }

        try {
            return new DateTimeImmutable(trim($value));
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseFloat(?string $value): float
    {
        if ($value === null) {
            return 0.0;
        }

        return (float) str_replace(',', '.', trim((string) $value));
    }

    private function isHeader(array $line): bool
    {
        $value = strtolower(trim($line[0] ?? ''));
        return str_contains($value, 'timestamp') || str_contains($value, 'date');
    }
}
