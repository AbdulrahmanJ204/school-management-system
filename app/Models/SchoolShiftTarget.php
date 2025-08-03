<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolShiftTarget extends Model
{
    protected $fillable = [
        'school_shift_id',
        'section_id',
        'grade_id',
    ];
    public function schoolShift()
    {
        return $this->belongsTo(SchoolShift::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
