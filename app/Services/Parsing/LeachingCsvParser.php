<?php

namespace App\Services\Parsing;

class LeachingCsvParser
{
    public function parse(string $path): array
    {
        $handle = fopen($path, 'r');
        if (!$handle) {
            throw new \RuntimeException('Unable to open leaching CSV.');
        }

        $pairs = [];
        while (($line = fgetcsv($handle, 0, ';')) !== false) {
            if ($this->isHeader($line)) {
                continue;
            }

            [$runoff, $emission] = array_pad($line, 2, null);
            $runoffValue = $this->parseFloat($runoff);
            $emissionValue = $this->parseFloat($emission);

            if ($runoffValue <= 0 || $emissionValue < 0) {
                continue;
            }

            $pairs[] = [
                'runoff' => $runoffValue,
                'emission' => $emissionValue,
            ];
        }

        fclose($handle);

        return $pairs;
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
        return str_contains($value, 'runoff') || str_contains($value, 'cumulative');
    }
}
