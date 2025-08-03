<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileTarget extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'section_id', 'grade_id', 'file_id','created_by'
    ];
    public function file() :BelongsTo{
        return $this->belongsTo(File::class);
    }
    public function section() :BelongsTo{
        return $this->belongsTo(Section::class);
    }
    public function grade() :BelongsTo{
        return $this->belongsTo(Grade::class);
    }
}
