<?php

namespace App\Http\Controllers;

use App\Models\Calculation;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function downloadReport(Calculation $calculation)
    {
        $result = $calculation->result;
        if (!$result || !$result->report_path) {
            abort(404);
        }

        return Storage::disk('public')->download($result->report_path);
    }

    public function downloadExport(Calculation $calculation)
    {
        $result = $calculation->result;
        if (!$result || !$result->export_path) {
            abort(404);
        }

        return Storage::disk('public')->download($result->export_path);
    }
}
