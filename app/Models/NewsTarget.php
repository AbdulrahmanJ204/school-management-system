<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsTarget extends Model
{
    use SoftDeletes , HasFactory;
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
