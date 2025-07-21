<?php

namespace App\Services;

use App\Exceptions\ImageUploadFailed;
use App\Exceptions\PermissionException;
use App\Exceptions\UserNotFoundException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function getUser($id)
    {
        $user = User::select([
            'id', 'first_name', 'father_name', 'last_name', 'email',
            'user_type', 'birth_date', 'gender', 'phone', 'image','last_login'
        ])->with('devices')->find($id);

        if (!$user) {
            throw new UserNotFoundException();
        }

        match ($user->user_type) {
            'admin' => $user->load('admin.createdBy'),
            'teacher' => $user->load('teacher.createdBy'),
            'student' => $user->load('student.createdBy'),
        };

        return ResponseHelper::jsonResponse(
            new UserResource($user),
            __('messages.user.get')
        );
    }
    public function updateUser($request, $id)
    {
        $admin = auth()->user();

        if (!$admin->hasPermissionTo('تعديل مستخدم')) {
            throw new PermissionException();
        }

        $user = User::select([
            'id', 'first_name', 'father_name', 'last_name', 'email',
            'user_type', 'birth_date', 'gender', 'phone', 'image'
        ])->find($id);

        if (!$user) {
            throw new UserNotFoundException();
        }

        $credentials = $request->validated();

        if ($request->hasFile('image'))
        {
            try {
                if ($user->image && $user->image !== 'user_images/default.png') {
                    Storage::disk('public')->delete($user->image);
                }

                $credentials['image'] = $request->file('image')->store('user_images', 'public');

                $user->image = $credentials['image'];
                $user->save();

            } catch (\Exception $e) {
                throw new ImageUploadFailed();
            }
        }

        DB::transaction(function () use ($user, $credentials) {

            $user->update($credentials);

            match ($user->user_type) {
                'admin' => $user->admin->touch(),
                'teacher' => $user->teacher->touch(),
                'student' => $user->student->update([
                    'updated_at' => now(),
                    'grandfather' => $credentials['grandfather'] ?? $user->student->grandfather,
                    'general_id'  => $credentials['general_id'] ?? $user->student->general_id,
                    'is_active' => $credentials['is_active'] ?? $user->student->is_active,
                ])
            };
        });

        return ResponseHelper::jsonResponse(
            new UserResource($user),
            __('messages.user.updated'),
            201,
            true
        );
    }
    public function deleteUser(int $id)
    {
        $admin = auth()->user();

        if (!$admin->hasPermissionTo('حذف مستخدم')) {
            throw new PermissionException();
        }

        $user = User::find($id);

        if (!$user) {
            throw new UserNotFoundException();
        }

        DB::transaction(function () use ($user) {

            match ($user->user_type) {
                'admin' => $user->admin?->delete(),
                'teacher' => $user->teacher?->delete(),
                'student' => $user->student?->delete()
            };

            if ($user->image !== 'user_images/default.png') {
                Storage::disk('public')->delete($user->image);
            }

            $user->delete();
        });

        return ResponseHelper::jsonResponse(
            null,
            __('messages.user.deleted'),
            200,
            true
        );
    }
    public function listAdminsAndTeachers()
    {
        if (!auth()->user()->hasPermissionTo('عرض المشرفين و الاساتذة')) {
            throw new PermissionException();
        }

        $users = User::select('id', 'first_name', 'father_name', 'last_name', 'gender', 'birth_date', 'email', 'phone', 'user_type', 'image')
            ->whereIn('user_type', ['admin', 'teacher'])
            ->with(['admin', 'teacher'])
            ->orderBy('id', 'asc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            UserResource::collection($users),
            __('messages.user.list_admins_and_teachers'),
        );
    }
}
