<?php

namespace App\Console\Commands;

use App\Models\CalculationJob;
use App\Services\Exports\ExportBuilder;
use App\Services\Model\SimulationRunner;
use App\Services\Reporting\ReportBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RunComleamQueue extends Command
{
    protected $signature = 'comleam:run-queue {--max=1 : Maximum number of jobs to process}';
    protected $description = 'Process queued COMLEAM calculations.';

    public function handle(SimulationRunner $runner, ReportBuilder $reportBuilder, ExportBuilder $exportBuilder): int
    {
        $max = (int) $this->option('max');
        $jobs = CalculationJob::where('status', 'queued')->limit($max)->get();

        foreach ($jobs as $job) {
            DB::transaction(function () use ($job) {
                $job->update([
                    'status' => 'running',
                    'started_at' => now(),
                    'attempts' => $job->attempts + 1,
                ]);
                $job->calculation->update(['status' => 'running']);
            });

            try {
                $calculation = $job->calculation->load('geometry.rows', 'weather', 'emissionFunction');
                $result = $runner->run($calculation);

                $reportPath = $reportBuilder->build($calculation, $result['summary'], $result['timeseries_path']);
                $exportPath = $exportBuilder->build($calculation, $result['summary'], $result['timeseries_path']);

                $calculation->result()->create([
                    'summary' => $result['summary'],
                    'timeseries_path' => $result['timeseries_path'],
                    'report_path' => $reportPath,
                    'export_path' => $exportPath,
                ]);

                $calculation->update(['status' => 'finished']);
                $job->update([
                    'status' => 'finished',
                    'finished_at' => now(),
                ]);
            } catch (\Throwable $e) {
                $job->update([
                    'status' => 'failed',
                    'finished_at' => now(),
                    'error' => $e->getMessage(),
                ]);
                $job->calculation->update([
                    'status' => 'failed',
                    'logs' => [
                        'error' => $e->getMessage(),
                    ],
                ]);
            }
        }

        return self::SUCCESS;
    }
}
