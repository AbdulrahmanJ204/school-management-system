<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileDownload extends Model
{
    protected $fillable = [
        'user_id',
        'file_id',
        'ip_address',
    ];
}
