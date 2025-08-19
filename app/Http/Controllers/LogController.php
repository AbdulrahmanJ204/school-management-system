<?php

namespace App\Http\Controllers;

use App\Models\DailyLogReport;
use App\Services\LoggingService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LogController extends Controller
{
    protected $loggingService;

    public function __construct(LoggingService $loggingService)
    {
        $this->loggingService = $loggingService;
    }



    /**
     * Get daily log reports.
     */
    public function getDailyReports(Request $request)
    {
        $query = DailyLogReport::query();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        $reports = $query->orderBy('report_date', 'desc')
            ->paginate($request->get('per_page', 15));

        return ResponseHelper::success($reports);
    }

    /**
     * Generate daily report for a specific date.
     */
    public function generateDailyReport(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        try {
            $date = Carbon::createFromFormat('Y-m-d', $request->date);
            $report = $this->loggingService->generateDailyReport($date);

            return ResponseHelper::success($report, 'Daily report generated successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to generate daily report: ' . $e->getMessage());
        }
    }

    /**
     * Download PDF report.
     */
    public function downloadPdfReport($id)
    {
        $report = DailyLogReport::findOrFail($id);

        if (!$report->hasPdfReport()) {
            return ResponseHelper::error('PDF report not found');
        }

        return Storage::download($report->pdf_path);
    }

    /**
     * Download Excel report.
     */
    public function downloadExcelReport($id)
    {
        $report = DailyLogReport::findOrFail($id);

        if (!$report->hasExcelReport()) {
            return ResponseHelper::error('Excel report not found');
        }

        return Storage::download($report->excel_path);
    }



    /**
     * Clean old logs.
     */
    public function cleanOldLogs(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        try {
            $deletedCount = $this->loggingService->cleanOldLogs($request->days);

            return ResponseHelper::success([
                'deleted_count' => $deletedCount
            ], "Successfully cleaned {$deletedCount} old log records");
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to clean old logs: ' . $e->getMessage());
        }
    }



    /**
     * Get available user types for filtering.
     */
    public function getUserTypes()
    {
        return ResponseHelper::success(ActivityLog::getUserTypes());
    }



    /**
     * Get available table names for filtering.
     */
    public function getTableNames()
    {
        $tableNames = ActivityLog::distinct()->pluck('table_name')->filter()->values();

        return ResponseHelper::success($tableNames);
    }
}
