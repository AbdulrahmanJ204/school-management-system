<?php

namespace App\Models;

use App\Traits\TargetsScopes;
use Illuminate\Database\Eloquent\Model;

class SchoolShiftTarget extends Model
{
    use TargetsScopes;
    
    protected $fillable = [
        'school_shift_id',
        'section_id',
        'grade_id',
        'created_by',
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
