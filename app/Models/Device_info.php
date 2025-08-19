<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device_info extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand',
        'device',
        'manufacturer',
        'model',
        'product',
        'name',
        'identifier',
        'os_version',
        'os_name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_devices', 'device_id', 'user_id')
            ->withTimestamps();
    }
}
