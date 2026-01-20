<?php

namespace Tests\Unit;

use App\Services\Parsing\WeatherCsvParser;
use PHPUnit\Framework\TestCase;

class WeatherCsvParserTest extends TestCase
{
    public function test_parses_weather_csv(): void
    {
        $csv = "timestamp;precipitation_mm;temperature_c;wind_speed_ms;humidity_pct\n2024-01-01 00:00:00;1.2;5;3;70\n";
        $path = sys_get_temp_dir().'/weather.csv';
        file_put_contents($path, $csv);

        $parser = new WeatherCsvParser();
        $result = $parser->parse($path);

        $this->assertSame(1, $result['summary']['row_count']);
        $this->assertSame(1.2, $result['summary']['total_precipitation_mm']);
        $this->assertCount(1, $result['rows']);
    }
}
