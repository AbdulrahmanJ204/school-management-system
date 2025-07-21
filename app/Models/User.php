<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasRoles, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'father_name',
        'birth_date',
        'gender',
        'email',
        'user_name',
        'password',
        'phone',
        'image',
        'role',
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
            ->withTimestamps();
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
}
