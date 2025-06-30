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
            'email' => $this->email,
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'role' => $this->role,
            'image' => $this->image ? asset('storage/' . $this->image) : asset('storage/user_images/default.png'),
            'last_login' => $this->when($isGetUserRoute, function () {
                return $this->last_login ? $this->last_login : 'This user has never logged in';
            }),

            'role_details' => match ($this->role) {
                 'admin' => [
                     'created_by' => $this->admin?->createdBy
                         ? trim("{$this->admin->createdBy->first_name} {$this->admin->createdBy->father_name} {$this->admin->createdBy->last_name}")
                         : null,
                     'created_at' => $this->admin?->created_at,
                     'updated_at' => $this->admin?->updated_at,
                 ],
                 'teacher' => [
                     'created_by' => $this->teacher?->createdBy
                         ? trim("{$this->teacher->createdBy->first_name} {$this->teacher->createdBy->father_name} {$this->teacher->createdBy->last_name}")
                         : null,
                     'created_at' => $this->teacher?->created_at,
                     'updated_at' => $this->teacher?->updated_at,
                 ],
                 'student' => [
                     'grandfather' => $this->student?->grandfather,
                     'general_id'  => $this->student?->general_id,
                     'is_active' => $this->student?->is_active,
                     'created_by' => $this->student?->createdBy
                         ? trim("{$this->student->createdBy->first_name} {$this->student->createdBy->father_name} {$this->student->createdBy->last_name}")
                         : null,
                     'created_at' => $this->student?->created_at,
                     'updated_at' => $this->student?->updated_at,
                 ],
             },

            'devices' => $this->when($isGetUserRoute, function () {
                return $this->devices->map(function ($device) {
                    return [
                        'last_login' => $this->last_login,
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
