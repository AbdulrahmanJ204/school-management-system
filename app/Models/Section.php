<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    //

    public function enrollments() : HasMany{
        return $this->hasMany(StudentEntrollment::class);
    }
}
