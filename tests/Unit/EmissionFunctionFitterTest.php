<?php

namespace Tests\Unit;

use App\Services\Model\EmissionFunctionFitter;
use PHPUnit\Framework\TestCase;

class EmissionFunctionFitterTest extends TestCase
{
    public function test_fits_linear_function(): void
    {
        $pairs = [
            ['runoff' => 1, 'emission' => 2],
            ['runoff' => 2, 'emission' => 4],
            ['runoff' => 3, 'emission' => 6],
        ];

        $fitter = new EmissionFunctionFitter();
        $result = $fitter->fit('linear', $pairs);

        $this->assertEqualsWithDelta(2.0, $result['parameters']['a'], 0.01);
        $this->assertArrayHasKey('rmse', $result['diagnostics']);
    }
}
