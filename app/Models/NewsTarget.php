<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsTarget extends Model
{
    protected $fillable = [
        'news_id',
        'grade_id',
        'section_id',
        'created_by'
    ];

    public function news() :  BelongsTo{
        return $this->belongsTo(News::class, 'news_id');
    }

}
