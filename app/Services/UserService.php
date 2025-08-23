<?php

namespace App\Services;

use App\Exceptions\ImageUploadFailed;
use App\Exceptions\PermissionException;
use App\Exceptions\UserNotFoundException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use App\Models\Grade;
use App\Models\User;
use App\Models\StudentEnrollment;
use App\Models\Semester;
use App\Models\Year;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * @throws UserNotFoundException
     */
    public function getUser($id): JsonResponse
    {
        $user = User::with('devices')->find($id);

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

    /**
     * @throws PermissionException
     * @throws ImageUploadFailed
     * @throws UserNotFoundException
     */
    public function updateUser($request, $id): JsonResponse
    {
        $admin = auth()->user();

        if (!$admin->hasPermissionTo('تعديل مستخدم')) {
            throw new PermissionException();
        }

        $user = User::findOrFail($id);

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

            if (isset($credentials['role_id'])) {
                $role = Role::findOrFail($credentials['role_id']);
                $user->syncRoles([$role->name]);
            }

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

            // Update student enrollment if GPA or grade_id is provided
            if ($user->user_type === 'student' && (isset($credentials['last_year_gpa']) || isset($credentials['grade_id']))) {
                // Get the current active year and first semester
                $year = Grade::where('id', $credentials['grade_id'])->first()->year;
                if ($year) {
                    $firstSemester = Semester::where('year_id', $year->id)
                        ->orderBy('start_date', 'asc')
                        ->first();

                    if ($firstSemester) {
                        // Find existing enrollment or create new one
                        $enrollment = StudentEnrollment::where('student_id', $user->student->id)
                            ->where('semester_id', $firstSemester->id)
                            ->first();

                        if ($enrollment) {
                            // Update existing enrollment
                            $updateData = [];
                            if (isset($credentials['last_year_gpa'])) {
                                $updateData['last_year_gpa'] = $credentials['last_year_gpa'];
                            }
                            if (isset($credentials['grade_id'])) {
                                $updateData['grade_id'] = $credentials['grade_id'];
                            }

                            if (!empty($updateData)) {
                                $enrollment->update($updateData);
                            }
                        } else {
                            // Create new enrollment if none exists
                            StudentEnrollment::create([
                                'student_id' => $user->student->id,
                                'grade_id' => $credentials['grade_id'] ?? 1, // Default to grade 1 if not provided
                                'section_id' => null,
                                'semester_id' => $firstSemester->id,
                                'year_id' => $year->id,
                                'last_year_gpa' => $credentials['last_year_gpa'] ?? null,
                                'created_by' => $admin->id,
                            ]);
                        }
                    }
                }
            }
        });

        return ResponseHelper::jsonResponse(
            new UserResource($user),
            __('messages.user.updated'),
            201,
            true
        );
    }

    /**
     * @throws PermissionException
     * @throws UserNotFoundException
     */
    public function deleteUser(int $id): JsonResponse
    {
        $admin = auth()->user();

        if (!$admin->hasPermissionTo('حذف مستخدم')) {
            throw new PermissionException();
        }

        $user = User::findOrFail($id);

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
        );
    }

    /**
     * @throws PermissionException
     */
    public function listAdminsAndTeachers(): JsonResponse
    {
        if (!auth()->user()->hasPermissionTo('عرض المشرفين و الاساتذة')) {
            throw new PermissionException();
        }

        $users = User::whereIn('user_type', ['admin', 'teacher'])
            ->with(['admin', 'teacher', 'roles.permissions'])
            ->orderBy('first_name', 'asc')
            ->paginate(50);

        return ResponseHelper::jsonResponse(
            UserResource::collection($users),
            __('messages.user.list_admins_and_teachers'),
            200,
            true,
            $users->lastPage()
        );
    }
}
