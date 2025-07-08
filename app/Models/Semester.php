<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Semester extends Model
{
    //
    public function year() : BelongsTo{
        return $this->belongsTo(Year::class);
    }
    public function student() : BelongsTo{
        return $this->belongsTo(Student::class);
    }
    public function section() : BelongsTo{
        $this->belongsTo(Section::class);
    }
}
