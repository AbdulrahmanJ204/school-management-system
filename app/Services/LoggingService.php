<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\ErrorLog;
use App\Models\DailyLogReport;
use App\Models\User;
use App\Notifications\DailyLogReportNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

class LoggingService
{
    /**
     * Log an activity.
     */
    public function logActivity(
        string $action,
        ?string $tableName = null,
        ?int $recordId = null,
        ?array $changes = null,
        ?Request $request = null
    ): ActivityLog {
        $request = $request ?? request();
        
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'user_type' => $this->getUserType(),
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'changes' => $changes,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Log an error.
     */
    public function logError(
        \Throwable $exception,
        ?Request $request = null
    ): ErrorLog {
        $request = $request ?? request();
        
        return ErrorLog::create([
            'user_id' => Auth::id(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'input' => $request->all(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Generate daily log report.
     */
    public function generateDailyReport(Carbon $date = null): DailyLogReport
    {
        $date = $date ?? Carbon::yesterday();
        $dateString = $date->format('Y-m-d');

        // Check if report already exists
        $existingReport = DailyLogReport::where('report_date', $dateString)->first();
        if ($existingReport) {
            // Delete existing PDF and Excel files if they exist
            if ($existingReport->pdf_path && Storage::exists($existingReport->pdf_path)) {
                Storage::delete($existingReport->pdf_path);
            }
            if ($existingReport->excel_path && Storage::exists($existingReport->excel_path)) {
                Storage::delete($existingReport->excel_path);
            }
            
            // Delete the existing report record
            $existingReport->delete();
        }

        // Get logs for the date
        $activityLogs = ActivityLog::whereDate('created_at', $dateString)->get();
        $errorLogs = ErrorLog::whereDate('created_at', $dateString)->get();
        $totalLogs = $activityLogs->count() + $errorLogs->count();

        // Create report record
        $report = DailyLogReport::create([
            'report_date' => $dateString,
            'total_logs' => $totalLogs,
        ]);

        try {
            // Generate PDF and Excel reports
            $this->generatePdfReport($report, $activityLogs, $errorLogs);
            $this->generateExcelReport($report, $activityLogs, $errorLogs);
        } catch (\Exception $e) {
            \Log::error('Report generation failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
        }

        return $report;
    }

    /**
     * Generate PDF report.
     */
    private function generatePdfReport(DailyLogReport $report, $activityLogs, $errorLogs): void
    {
        try {
            // Configure DomPDF
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            
            $dompdf = new Dompdf($options);
            
            // Get the HTML content from the view
            $html = view('reports.daily-log-pdf', [
                'report' => $report,
                'activityLogs' => $activityLogs,
                'errorLogs' => $errorLogs,
                'date' => $report->report_date,
            ])->render();
            
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $filename = "daily-log-report-{$report->report_date->format('Y-m-d')}.pdf";
            $path = "reports/logs/{$filename}";
            
            // Ensure the directory exists
            Storage::makeDirectory('reports/logs');
            
            // Save the PDF
            Storage::put($path, $dompdf->output());
            
            $report->update(['pdf_path' => $path]);
        } catch (\Exception $e) {
            \Log::error('Failed to generate PDF report: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate Excel report.
     */
    private function generateExcelReport(DailyLogReport $report, $activityLogs, $errorLogs): void
    {
        try {
            $spreadsheet = new Spreadsheet();
            
            // Activity Logs Sheet
            $activitySheet = $spreadsheet->getActiveSheet();
            $activitySheet->setTitle('Activity Logs');
            
                         // Headers
             $activitySheet->setCellValue('A1', 'ID');
             $activitySheet->setCellValue('B1', 'User ID');
             $activitySheet->setCellValue('C1', 'User Type');
             $activitySheet->setCellValue('D1', 'Action');
             $activitySheet->setCellValue('E1', 'Table Name');
             $activitySheet->setCellValue('F1', 'Record ID');
             $activitySheet->setCellValue('G1', 'Changes');
             $activitySheet->setCellValue('H1', 'IP Address');
             $activitySheet->setCellValue('I1', 'User Agent');
             $activitySheet->setCellValue('J1', 'Created At');
            
                         $row = 2;
             foreach ($activityLogs as $log) {
                 $activitySheet->setCellValue("A{$row}", $log->id);
                 $activitySheet->setCellValue("B{$row}", $log->user_id);
                 $activitySheet->setCellValue("C{$row}", $log->user_type);
                 $activitySheet->setCellValue("D{$row}", $log->action);
                 $activitySheet->setCellValue("E{$row}", $log->table_name);
                 $activitySheet->setCellValue("F{$row}", $log->record_id);
                 $activitySheet->setCellValue("G{$row}", $log->changes ? json_encode($log->changes) : '');
                 $activitySheet->setCellValue("H{$row}", $log->ip_address);
                 $activitySheet->setCellValue("I{$row}", $log->user_agent);
                 $activitySheet->setCellValue("J{$row}", $log->created_at);
                 $row++;
             }
            
            // Error Logs Sheet
            $errorSheet = $spreadsheet->createSheet();
            $errorSheet->setTitle('Error Logs');
            
                         // Headers
             $errorSheet->setCellValue('A1', 'ID');
             $errorSheet->setCellValue('B1', 'User ID');
             $errorSheet->setCellValue('C1', 'Code');
             $errorSheet->setCellValue('D1', 'File');
             $errorSheet->setCellValue('E1', 'Line');
             $errorSheet->setCellValue('F1', 'Message');
             $errorSheet->setCellValue('G1', 'URL');
             $errorSheet->setCellValue('H1', 'Method');
             $errorSheet->setCellValue('I1', 'Input');
             $errorSheet->setCellValue('J1', 'IP Address');
             $errorSheet->setCellValue('K1', 'User Agent');
             $errorSheet->setCellValue('L1', 'Created At');
            
                         $row = 2;
             foreach ($errorLogs as $log) {
                 $errorSheet->setCellValue("A{$row}", $log->id);
                 $errorSheet->setCellValue("B{$row}", $log->user_id);
                 $errorSheet->setCellValue("C{$row}", $log->code);
                 $errorSheet->setCellValue("D{$row}", $log->file);
                 $errorSheet->setCellValue("E{$row}", $log->line);
                 $errorSheet->setCellValue("F{$row}", $log->message);
                 $errorSheet->setCellValue("G{$row}", $log->url);
                 $errorSheet->setCellValue("H{$row}", $log->method);
                 $errorSheet->setCellValue("I{$row}", $log->input ? json_encode($log->input) : '');
                 $errorSheet->setCellValue("J{$row}", $log->ip_address);
                 $errorSheet->setCellValue("K{$row}", $log->user_agent);
                 $errorSheet->setCellValue("L{$row}", $log->created_at);
                 $row++;
             }
            
            // Summary Sheet
            $summarySheet = $spreadsheet->createSheet();
            $summarySheet->setTitle('Summary');
            
            $summarySheet->setCellValue('A1', 'Report Date');
            $summarySheet->setCellValue('B1', $report->report_date->format('Y-m-d'));
            
            $summarySheet->setCellValue('A3', 'Total Activity Logs');
            $summarySheet->setCellValue('B3', $activityLogs->count());
            
            $summarySheet->setCellValue('A4', 'Total Error Logs');
            $summarySheet->setCellValue('B4', $errorLogs->count());
            
            $summarySheet->setCellValue('A5', 'Total Logs');
            $summarySheet->setCellValue('B5', $report->total_logs);
            
            // Ensure the directory exists
            Storage::makeDirectory('reports/logs');
            
            // Save file
            $writer = new Xlsx($spreadsheet);
            $filename = "daily-log-report-{$report->report_date->format('Y-m-d')}.xlsx";
            $path = "reports/logs/{$filename}";
            
            // Create temporary file and save to storage
            $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
            $writer->save($tempFile);
            
            // Copy to storage
            Storage::put($path, file_get_contents($tempFile));
            unlink($tempFile); // Clean up temp file
            
            $report->update(['excel_path' => $path]);
        } catch (\Exception $e) {
            \Log::error('Failed to generate Excel report: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get user type based on authenticated user.
     */
    private function getUserType(): ?string
    {
        if (!Auth::check()) {
            return null;
        }

        $user = Auth::user();
        
        if ($user->admin) {
            return 'admin';
        }
        
        if ($user->teacher) {
            return 'teacher';
        }
        
        if ($user->student) {
            return 'student';
        }
        
        return null;
    }

    /**
     * Clean old logs (older than specified days).
     */
    public function cleanOldLogs(int $days = 90): int
    {
        $cutoffDate = Carbon::now()->subDays($days);
        
        $activityLogsDeleted = ActivityLog::where('created_at', '<', $cutoffDate)->delete();
        $errorLogsDeleted = ErrorLog::where('created_at', '<', $cutoffDate)->delete();
        
        return $activityLogsDeleted + $errorLogsDeleted;
    }



         /**
     * Send daily log report to administrators with owner role.
     */
    public function sendDailyReportToAdmins(DailyLogReport $report): int
    {
        // Dispatch the job to handle email sending in background
        \App\Jobs\SendDailyLogReportJob::dispatch($report);
        
        \Log::info("SendDailyLogReportJob dispatched for report ID: {$report->id}");
        
        // Return estimated count based on admin users with owner permission
        $adminCount = User::where('user_type','admin')
            ->whereHas('roles', function ($query) {
                $query->where('name', 'owner');
            })->count();
        
        return $adminCount;
    }

     /**
      * Generate and send daily report automatically.
      */
     public function generateAndSendDailyReport(Carbon $date = null): array
     {
         $date = $date ?? Carbon::yesterday();
         
         try {
             // Generate the daily report
             $report = $this->generateDailyReport($date);
             
             // Send to administrators
             $sentCount = $this->sendDailyReportToAdmins($report);
             
             return [
                 'success' => true,
                 'report' => $report,
                 'sent_count' => $sentCount,
                 'message' => "Daily report generated and sent to {$sentCount} administrators"
             ];
         } catch (\Exception $e) {
             \Log::error('Failed to generate and send daily report: ' . $e->getMessage());
             
             return [
                 'success' => false,
                 'error' => $e->getMessage(),
                 'message' => 'Failed to generate and send daily report'
             ];
         }
     }
 }
