<?php

namespace App\Http\Resources;

use App\Http\Resources\Basic\GradeBasicResource;
use App\Http\Resources\Basic\SectionBasicResource;
use App\Http\Resources\BaseResource;
use App\Enums\UserType;
use Illuminate\Http\Request;

/**
 * User Resource - Complete user information
 * مورد المستخدم - معلومات المستخدم الكاملة
 * Uses basic data structures to avoid circular dependencies
 * يستخدم هياكل البيانات الأساسية لتجنب التضارب الدوري
 */
class UserResource extends BaseResource
{
    public function toArray($request): array
    {
        $isGetStaffRoute = $request->routeIs('staff');
        $isGetAdminsRoute = $request->routeIs('admins');
        $isUpdateUserRoute = $request->routeIs('user.update');
        $isLoginRoute = $request->routeIs('auth.login');
        $isGetUserRoute = $request->routeIs('users.show');

        $baseData = [
            'id' => $this->id,
            'full_name' => trim("{$this->first_name} {$this->father_name} {$this->last_name}"),
            'first_name' => $this->first_name,
            'father_name' => $this->father_name,
            'last_name' => $this->last_name,
            'mother_name' => $this->mother_name,
            'email' => $this->email,
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'user_type' => $this->user_type,
            'image' => $this->image ? asset('storage/' . $this->image) : asset('storage/user_images/default.png'),
        ];

        if ($this->user_type === 'student') {
            $baseData['student_id'] = $this->student->id;
        }
        if ($this->user_type === 'admin') {
            $baseData['admin_id'] = $this->admin->id;
        }
        if ($this->user_type === 'teacher') {
            $baseData['teacher_id'] = $this->teacher->id;
        }

        if ($this->user_type !== 'student') {
            $baseData['email'] = $this->email;
        } else {
            $baseData['last_year_gpa'] = $this->student?->studentEnrollments()
                ->latest('year_id')
                ->first();
        }

        // Add tokens for login response
        if ($isLoginRoute && isset($this->access_token) && isset($this->refresh_token)) {
            $baseData['access_token'] = $this->access_token;
            $baseData['refresh_token'] = $this->refresh_token;
        }

        // Add role and permissions for login and staff routes
        if ($isLoginRoute || $isGetStaffRoute || $isGetAdminsRoute || $isUpdateUserRoute || $isGetUserRoute) {
            $role = $this->roles->first();
            $baseData['role'] = $role ? [
                'id' => $role->id,
                'name' => $role->name,
            ] : null;

            $baseData['permissions'] = $role ? $role->permissions->pluck('name') : [];
        }

        // Add devices for login and user routes
        if ($isLoginRoute || $isGetUserRoute || $isGetAdminsRoute || $isUpdateUserRoute) {
            $baseData['devices'] = $this->devices->map(function ($device) {
                return [
                    'last_login' => $this->last_login ? $this->last_login->format('Y-m-d H:i:s') : null,
                    'brand' => $device->brand,
                    'device' => $device->device,
                    'manufacturer' => $device->manufacturer,
                    'model' => $device->model,
                    'product' => $device->product,
                    'name' => $device->name,
                    'identifier' => $device->identifier,
                    'os_version' => $device->os_version,
                    'os_name' => $device->os_name,
                ];
            });
        }

        // Add conditional fields
        $baseData['last_login'] = $this->when($isGetUserRoute, function () {
            return $this->last_login ? $this->last_login->format('Y-m-d H:i:s') : 'This user has never logged in';
        });

        $baseData['grand_father_name'] = $this->when($this->user_type == 'student', function () {
            return $this->student?->grandfather;
        });

        $baseData['general_id'] = $this->when($this->user_type == 'student', function () {
            return $this->student?->general_id;
        });

        $baseData['is_teacher'] = $this->when($this->user_type == 'teacher', function () {
            return true;
        });

        // Use basic data structures to avoid circular dependencies
        // استخدام هياكل البيانات الأساسية لتجنب التضارب الدوري
        $baseData['grade_summary'] = $this->when($this->user_type == 'student', function () {
            return [
                'id' => $this->student?->studentEnrollments->first()?->grade?->id,
                'grade_name' => $this->student?->studentEnrollments->first()?->grade?->title
            ];
        });

        $baseData['section'] = $this->when($this->user_type == 'student', function () {
            return [
                'id' => $this->student?->studentEnrollments->first()?->section?->id,
                'section_name' => $this->student?->studentEnrollments->first()?->section?->title,
                'grade_id' => $this->student?->studentEnrollments->first()?->section?->grade?->id
            ];
        });

        $baseData['year'] = $this->when($this->user_type == 'student', function () {
            return [
                'id' => $this->student?->studentEnrollments->first()?->year?->id,
                'name' => $this->student?->studentEnrollments->first()?->year?->name,
                'start_date' => $this->student?->studentEnrollments->first()?->year?->start_date,
                'end_date' => $this->student?->studentEnrollments->first()?->year?->end_date,
                'is_active' => $this->student?->studentEnrollments->first()?->year?->is_active
            ];
        });

        $baseData['semester'] = $this->when($this->user_type == 'student', function () {
            return [
                'id' => $this->student?->studentEnrollments->first()?->semester?->id,
                'name' => $this->student?->studentEnrollments->first()?->semester?->name,
                'start_date' => $this->student?->studentEnrollments->first()?->semester?->start_date,
                'end_date' => $this->student?->studentEnrollments->first()?->semester?->end_date,
                'year_id' => $this->student?->studentEnrollments->first()?->year?->id,
                'is_active' => $this->student?->studentEnrollments->first()?->semester?->is_active
            ];
        });
        
        return $baseData;
    }
}
