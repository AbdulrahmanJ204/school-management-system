<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device_info extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'platform',
        'type',
        'name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_device', 'device_info_id', 'user_id')
            ->withTimestamps();
    }
}
