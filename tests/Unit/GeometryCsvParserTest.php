<?php

namespace Tests\Unit;

use App\Services\Parsing\GeometryCsvParser;
use PHPUnit\Framework\TestCase;

class GeometryCsvParserTest extends TestCase
{
    public function test_parses_geometry_csv(): void
    {
        $csv = "component_id;surface_area_m2;exposure_class;material_subtype_code\nA1;10;Roof;ROOF-METAL\n";
        $path = sys_get_temp_dir().'/geometry.csv';
        file_put_contents($path, $csv);

        $parser = new GeometryCsvParser();
        $result = $parser->parse($path);

        $this->assertSame(1, $result['summary']['component_count']);
        $this->assertSame(10.0, $result['summary']['total_surface_area_m2']);
        $this->assertCount(1, $result['rows']);
    }
}
