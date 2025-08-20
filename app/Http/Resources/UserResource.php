<?php

namespace App\Http\Resources;

use App\Enums\UserType;
use Illuminate\Http\Resources\Json\JsonResource;


class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        $isGetUserRoute = $request->routeIs('users.show');
        $isGetStaffRoute = $request->routeIs('staff');


        return [
            'id' => $this->id,
            'full_name' => trim("{$this->first_name} {$this->father_name} {$this->last_name}"),
            'first_name'=>$this->first_name,
            'father_name'=>$this->father_name,
            'last_name'=>$this->last_name,
            'mother_name' => $this->mother_name,
            'email' => $this->email,
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'user_type' => $this->user_type,
            'role' => $this->when($isGetStaffRoute, function () {
                $role = $this->roles->first();
                return $role ? [
                    'id' => $role->id,
                    'name' => $role->name,
                ] : null;
            }),
            'permissions' => $this->when($isGetStaffRoute, function () {
                $role = $this->roles->first(); // again assuming 1 role per user
                return $role ? $role->permissions->pluck('name') : [];
            }),
            'image' => $this->image ? asset('storage/' . $this->image) : asset('storage/user_images/default.png'),
            'last_login' => $this->when($isGetUserRoute, function () {
                return $this->last_login ? $this->last_login->format('Y-m-d H:i:s') : 'This user has never logged in';
            }),
            'grand_father_name' => $this->when($this->user_type == 'student', function () {
                return $this->student?->grandfather;
            }),
            'general_id'  => $this->when($this->user_type == 'student', function () {
                return $this->student?->general_id;
            }),
            'is_teacher' => $this->when($this->user_type == 'teacher', function () {
                return true;
            }),
            'grade_summary' => $this->when($this->user_type == 'student', function () {
                return [
                    'id' => $this->student?->studentEnrollments->first()?->section?->grade?->id,
                    'grade_name' => $this->student?->studentEnrollments->first()?->section?->grade?->title
                ];
            }),
            'section' => $this->when($this->user_type == 'student', function () {
                return [
                    'id' => $this->student?->studentEnrollments->first()?->section?->id,
                    'section_name' => $this->student?->studentEnrollments->first()?->section?->title,
                    'grade_id' => $this->student?->studentEnrollments->first()?->section?->grade?->id
                ];
            }),
            'year' => $this->when($this->user_type == 'student', function () {
                return [
                    'id' => $this->student?->studentEnrollments->first()?->year?->id,
                    'name' => $this->student?->studentEnrollments->first()?->year?->name,
                    'start_date' => $this->student?->studentEnrollments->first()?->year?->start_date,
                    'end_date' => $this->student?->studentEnrollments->first()?->year?->end_date,
                    'is_active' => $this->student?->studentEnrollments->first()?->year?->is_active
                ];
            }),
            'semester' => $this->when($this->user_type == 'student', function () {
                return [
                    'id' => $this->student?->studentEnrollments->first()?->semester?->id,
                    'name' => $this->student?->studentEnrollments->first()?->semester?->name,
                    'start_date' => $this->student?->studentEnrollments->first()?->semester?->start_date,
                    'end_date' => $this->student?->studentEnrollments->first()?->semester?->end_date,
                    'year_id' => $this->student?->studentEnrollments->first()?->year?->id,
                    'is_active' => $this->student?->studentEnrollments->first()?->semester?->is_active
                ];
            }),

            'devices' => $this->when($isGetUserRoute, function () {
                return $this->devices->map(function ($device) {
                    return [
                        'last_login' => $this->last_login->format('Y-m-d H:i:s'),
                        'brand'      => $device->brand,
                        'device'     => $device->device,
                        'manufacturer' => $device->manufacturer,
                        'model'      => $device->model,
                        'product'    => $device->product,
                        'name'       => $device->name,
                        'identifier' => $device->identifier,
                        'os_version' => $device->os_version,
                        'os_name'    => $device->os_name,
                    ];
                });
            }),
        ];
    }
}
