<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Year extends Model
{
    //
    public function semesters() : HasMany{
        return $this->hasMany(Semester::class);
    }
}
