<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\LogsActivity;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasRoles, HasApiTokens, HasFactory, Notifiable, LogsActivity, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'father_name',
        'mother_name',
        'birth_date',
        'gender',
        'email',
        'user_name',
        'password',
        'phone',
        'image',
        'user_type',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'last_login' => 'datetime',
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function devices()
    {
        return $this->belongsToMany(Device_info::class, 'user_devices', 'user_id', 'device_id')
            ->withTimestamps()
            ->withPivot('last_used_at');
    }
    public function admin()
    {
        return $this->hasOne(Admin::class, 'user_id');
    }
    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_id');
    }
    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }
    public function createdAdmins()
    {
        return $this->hasMany(Admin::class, 'created_by_id');
    }
    public function createdTeachers()
    {
        return $this->hasMany(Teacher::class, 'created_by_id');
    }
    public function createdStudents()
    {
        return $this->hasMany(Student::class, 'created_by_id');
    }

    /**
     * Find or create a device for this user based on device data
     */
    public function findOrCreateDevice(array $deviceData)
    {
        $identifier = $deviceData['identifier'] ?? null;

        if (!$identifier) {
            // If no identifier, always create a new device
            $device = Device_info::create($deviceData);
            $this->devices()->attach($device->id, ['last_used_at' => now()]);
            return $device;
        }

        // Try to find existing device with same identifier
        $existingDevice = Device_info::forUserByIdentifier($this->id, $identifier)->first();

        if ($existingDevice) {
            // Update existing device with new information
            $existingDevice->update($deviceData);
            // Update the last_used_at timestamp in the pivot table
            $this->devices()->updateExistingPivot($existingDevice->id, ['last_used_at' => now()]);
            return $existingDevice;
        }

        // Create new device
        $device = Device_info::create($deviceData);
        $this->devices()->attach($device->id, ['last_used_at' => now()]);
        return $device;
    }

    /**
     * Get devices ordered by last used time (most recent first)
     */
    public function getDevicesOrderedByLastUsed()
    {
        return $this->devices()
            ->orderBy('last_used_at', 'desc')
            ->get();
    }
}
