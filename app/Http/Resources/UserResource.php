<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        $isGetUserRoute = $request->routeIs('users.show');

        return [
            'id' => $this->id,
            'name' => trim("{$this->first_name} {$this->father_name} {$this->last_name}"),
            'mother_name' => $this->mother_name,
            'email' => $this->email,
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'user_type' => $this->user_type,
            'image' => $this->image ? asset('storage/' . $this->image) : asset('storage/user_images/default.png'),
            'last_login' => $this->when($isGetUserRoute, function () {
                return $this->last_login ? $this->last_login->format('Y-m-d H:i:s') : 'This user has never logged in';
            }),

            'user_type_details' => match ($this->user_type) {
                 'admin' => [
                     'created_by' => $this->admin?->createdBy
                         ? trim("{$this->admin->createdBy->first_name} {$this->admin->createdBy->father_name} {$this->admin->createdBy->last_name}")
                         : null,
                     'created_at' => $this->admin?->created_at->format('Y-m-d H:i:s'),
                     'updated_at' => $this->admin?->updated_at->format('Y-m-d H:i:s'),
                 ],
                 'teacher' => [
                     'created_by' => $this->teacher?->createdBy
                         ? trim("{$this->teacher->createdBy->first_name} {$this->teacher->createdBy->father_name} {$this->teacher->createdBy->last_name}")
                         : null,
                     'created_at' => $this->teacher?->created_at->format('Y-m-d H:i:s'),
                     'updated_at' => $this->teacher?->updated_at->format('Y-m-d H:i:s'),
                 ],
                 'student' => [
                     'grandfather' => $this->student?->grandfather,
                     'general_id'  => $this->student?->general_id,
                     'is_active' => $this->student?->is_active,
                     'created_by' => $this->student?->createdBy
                         ? trim("{$this->student->createdBy->first_name} {$this->student->createdBy->father_name} {$this->student->createdBy->last_name}")
                         : null,
//                     'grade' => $this->student?->studentEnrollments->first()->section->grade,
//                     'section' => [
//                         'id' => $this->student?->studentEnrollments->first()?->section?->id,
//                         'title' => $this->student?->studentEnrollments->first()?->section?->title,
//                     ],
                     'created_at' => $this->student?->created_at->format('Y-m-d H:i:s'),
                     'updated_at' => $this->student?->updated_at->format('Y-m-d H:i:s'),
                 ],
             },

            'devices' => $this->when($isGetUserRoute, function () {
                return $this->devices->map(function ($device) {
                    return [
                        'last_login' => $this->last_login->format('Y-m-d H:i:s'),
                        'device_id'  => $device->device_id,
                        'name'       => $device->name,
                        'type'       => $device->type,
                        'platform'   => $device->platform,
                    ];
                });
            }),
        ];
    }
}
