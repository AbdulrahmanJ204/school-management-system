<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyLogReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_date',
        'total_logs',
        'pdf_path',
        'excel_path',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    /**
     * Scope to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('report_date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by date.
     */
    public function scopeByDate($query, $date)
    {
        return $query->where('report_date', $date);
    }

    /**
     * Check if PDF report exists.
     */
    public function hasPdfReport(): bool
    {
        return !empty($this->pdf_path) && Storage::exists($this->pdf_path);
    }

    /**
     * Check if Excel report exists.
     */
    public function hasExcelReport(): bool
    {
        return !empty($this->excel_path) && Storage::exists($this->excel_path);
    }

    /**
     * Get the full PDF path.
     */
    public function getPdfFullPath(): ?string
    {
        return $this->pdf_path ? Storage::path($this->pdf_path) : null;
    }

    /**
     * Get the full Excel path.
     */
    public function getExcelFullPath(): ?string
    {
        return $this->excel_path ? Storage::path($this->excel_path) : null;
    }
}
