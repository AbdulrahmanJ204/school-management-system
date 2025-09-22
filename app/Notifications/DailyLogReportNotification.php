<?php

namespace App\Notifications;

use App\Models\DailyLogReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DailyLogReportNotification extends Notification
{
    use Queueable;

    protected DailyLogReport $report;

    public function __construct(DailyLogReport $report)
    {
        $this->report = $report;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        
        try {
            $message = (new MailMessage)
                ->subject('Daily Log Report - ' . $this->report->report_date->format('F j, Y'))
                ->greeting('Hello ' . $notifiable->first_name . ' ' . $notifiable->last_name . ',')
                ->line('Here is your daily log report for ' . $this->report->report_date->format('F j, Y') . '.')
                ->line('Total logs recorded: ' . $this->report->total_logs);
                // ->line('This report contains all activity and error logs from the previous day.');

            // Attach PDF if it exists
            if ($this->report->pdf_path && Storage::exists($this->report->pdf_path)) {
                try {
                    $pdfPath = Storage::path($this->report->pdf_path);
                    $pdfFilename = 'daily-log-report-' . $this->report->report_date->format('Y-m-d') . '.pdf';
                    
                    // Check if file actually exists on filesystem
                    if (file_exists($pdfPath)) {
                        $message->attach($pdfPath, [
                            'as' => $pdfFilename,
                            'mime' => 'application/pdf',
                        ]);
                        
                        Log::info("PDF attached successfully: {$pdfPath} as {$pdfFilename}");
                    } else {
                        Log::warning("PDF file not found on filesystem: {$pdfPath}");
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to attach PDF: " . $e->getMessage());
                    // Don't fail the entire notification, just log the error
                }
            } else {
                Log::info("No PDF to attach or PDF file doesn't exist in storage");
            }

            // Attach Excel if it exists
            if ($this->report->excel_path && Storage::exists($this->report->excel_path)) {
                try {
                    $excelPath = Storage::path($this->report->excel_path);
                    $excelFilename = 'daily-log-report-' . $this->report->report_date->format('Y-m-d') . '.xlsx';
                    
                    // Check if file actually exists on filesystem
                    if (file_exists($excelPath)) {
                        $message->attach($excelPath, [
                            'as' => $excelFilename,
                            'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ]);
                        
                        Log::info("Excel attached successfully: {$excelPath} as {$excelFilename}");
                    } else {
                        Log::warning("Excel file not found on filesystem: {$excelPath}");
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to attach Excel: " . $e->getMessage());
                    // Don't fail the entire notification, just log the error
                }
            } else {
                Log::info("No Excel to attach or Excel file doesn't exist in storage");
            }

            $finalMessage = $message
                // ->line('Please review the attached reports for any unusual activity or errors that may require attention.')
                ->line('If you have any questions, please contact the system administrator.')
                ->salutation('Best regards, School Management System');
            
            return $finalMessage;
            
        } catch (\Exception $e) {
            Log::error("Error in toMail() method: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function toArray(object $notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'report_date' => $this->report->report_date->format('Y-m-d'),
            'total_logs' => $this->report->total_logs,
        ];
    }

    public function failed(\Throwable $exception)
    {
        Log::error("DailyLogReportNotification failed: " . $exception->getMessage());
        Log::error("Stack trace: " . $exception->getTraceAsString());
    }
}