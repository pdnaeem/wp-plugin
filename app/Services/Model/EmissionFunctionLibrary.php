<?php

namespace App\Services\Model;

class EmissionFunctionLibrary
{
    public static function types(): array
    {
        return [
            'logarithmic' => 'Logarithmic',
            'langmuir' => 'Langmuir/Michaelis-Menten',
            'limited_growth' => 'Limited growth',
            'diffusion' => 'Diffusion controlled',
            'linear' => 'Linear',
        ];
    }

    public static function evaluate(string $type, float $runoff, array $parameters): float
    {
        return match ($type) {
            'logarithmic' => self::logarithmic($runoff, $parameters),
            'langmuir' => self::langmuir($runoff, $parameters),
            'limited_growth' => self::limitedGrowth($runoff, $parameters),
            'diffusion' => self::diffusion($runoff, $parameters),
            'linear' => self::linear($runoff, $parameters),
            default => 0.0,
        };
    }

    private static function logarithmic(float $runoff, array $parameters): float
    {
        $a = (float) ($parameters['a'] ?? 1.0);
        $b = (float) ($parameters['b'] ?? 1.0);

        return $a * log(1 + $b * $runoff);
    }

    private static function langmuir(float $runoff, array $parameters): float
    {
        $a = (float) ($parameters['a'] ?? 1.0);
        $b = (float) ($parameters['b'] ?? 1.0);

        return ($a * $runoff) / ($b + $runoff);
    }

    private static function limitedGrowth(float $runoff, array $parameters): float
    {
        $a = (float) ($parameters['a'] ?? 1.0);
        $b = (float) ($parameters['b'] ?? 0.1);

        return $a * (1 - exp(-$b * $runoff));
    }

    private static function diffusion(float $runoff, array $parameters): float
    {
        $a = (float) ($parameters['a'] ?? 1.0);

        return $a * sqrt(max($runoff, 0));
    }

    private static function linear(float $runoff, array $parameters): float
    {
        $a = (float) ($parameters['a'] ?? 1.0);

        return $a * $runoff;
    }
}
