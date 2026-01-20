<?php

namespace App\Services\Reporting;

use App\Models\Calculation;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Storage;

class ReportBuilder
{
    public function build(Calculation $calculation, array $summary, string $timeseriesPath): string
    {
        $html = view('reports.pdf', [
            'calculation' => $calculation,
            'summary' => $summary,
            'timeseriesPath' => $timeseriesPath,
        ])->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        Storage::disk('public')->makeDirectory('reports');
        $path = 'reports/calculation-'.$calculation->id.'.pdf';
        Storage::disk('public')->put($path, $dompdf->output());

        return $path;
    }
}
