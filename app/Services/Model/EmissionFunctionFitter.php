<?php

namespace App\Services\Model;

class EmissionFunctionFitter
{
    public static function types(): array
    {
        return array_keys(EmissionFunctionLibrary::types());
    }

    public function fit(string $type, array $pairs): array
    {
        $pairs = array_values(array_filter($pairs, fn ($pair) => $pair['runoff'] > 0));
        if (count($pairs) < 2) {
            return [
                'parameters' => ['a' => 0.0, 'b' => 0.0],
                'diagnostics' => ['rmse' => null, 'warning' => 'Not enough data points for fitting.'],
            ];
        }

        return match ($type) {
            'linear' => $this->fitLinear($pairs),
            'logarithmic' => $this->fitLogarithmic($pairs),
            'langmuir' => $this->fitLangmuir($pairs),
            'limited_growth' => $this->fitLimitedGrowth($pairs),
            'diffusion' => $this->fitDiffusion($pairs),
            default => [
                'parameters' => ['a' => 0.0],
                'diagnostics' => ['rmse' => null, 'warning' => 'Unknown function type.'],
            ],
        };
    }

    private function fitLinear(array $pairs): array
    {
        $sumXY = 0.0;
        $sumXX = 0.0;
        foreach ($pairs as $pair) {
            $sumXY += $pair['runoff'] * $pair['emission'];
            $sumXX += $pair['runoff'] ** 2;
        }
        $a = $sumXX > 0 ? $sumXY / $sumXX : 0.0;

        return $this->buildResult(['a' => $a], $pairs);
    }

    private function fitDiffusion(array $pairs): array
    {
        $sumXY = 0.0;
        $sumXX = 0.0;
        foreach ($pairs as $pair) {
            $x = sqrt($pair['runoff']);
            $sumXY += $x * $pair['emission'];
            $sumXX += $x ** 2;
        }
        $a = $sumXX > 0 ? $sumXY / $sumXX : 0.0;

        return $this->buildResult(['a' => $a], $pairs, 'diffusion');
    }

    private function fitLogarithmic(array $pairs): array
    {
        return $this->fitWithGrid('logarithmic', $pairs, 0.01, 5.0);
    }

    private function fitLangmuir(array $pairs): array
    {
        return $this->fitWithGrid('langmuir', $pairs, 0.01, 5.0);
    }

    private function fitLimitedGrowth(array $pairs): array
    {
        return $this->fitWithGrid('limited_growth', $pairs, 0.01, 2.0);
    }

    private function fitWithGrid(string $type, array $pairs, float $minB, float $maxB): array
    {
        $best = ['rmse' => PHP_FLOAT_MAX, 'parameters' => ['a' => 0.0, 'b' => $minB]];
        $steps = 30;
        for ($i = 0; $i <= $steps; $i++) {
            $b = $minB + ($maxB - $minB) * ($i / $steps);
            $a = $this->solveForA($type, $pairs, $b);
            $rmse = $this->calculateRmse($type, $pairs, ['a' => $a, 'b' => $b]);
            if ($rmse < $best['rmse']) {
                $best = ['rmse' => $rmse, 'parameters' => ['a' => $a, 'b' => $b]];
            }
        }

        return [
            'parameters' => $best['parameters'],
            'diagnostics' => [
                'rmse' => $best['rmse'],
                'warning' => $best['rmse'] > 0.2 ? 'Fit error is relatively high.' : null,
            ],
        ];
    }

    private function solveForA(string $type, array $pairs, float $b): float
    {
        $sumXY = 0.0;
        $sumXX = 0.0;
        foreach ($pairs as $pair) {
            $x = $this->basis($type, $pair['runoff'], $b);
            $sumXY += $x * $pair['emission'];
            $sumXX += $x ** 2;
        }

        return $sumXX > 0 ? $sumXY / $sumXX : 0.0;
    }

    private function basis(string $type, float $runoff, float $b): float
    {
        return match ($type) {
            'logarithmic' => log(1 + $b * $runoff),
            'langmuir' => $runoff / ($b + $runoff),
            'limited_growth' => 1 - exp(-$b * $runoff),
            default => $runoff,
        };
    }

    private function buildResult(array $parameters, array $pairs, string $type = 'linear'): array
    {
        return [
            'parameters' => $parameters,
            'diagnostics' => [
                'rmse' => $this->calculateRmse($type, $pairs, $parameters),
                'warning' => null,
            ],
        ];
    }

    private function calculateRmse(string $type, array $pairs, array $parameters): float
    {
        $sum = 0.0;
        $count = 0;
        foreach ($pairs as $pair) {
            $predicted = EmissionFunctionLibrary::evaluate($type, $pair['runoff'], $parameters);
            $sum += ($predicted - $pair['emission']) ** 2;
            $count++;
        }

        return $count ? sqrt($sum / $count) : 0.0;
    }
}
