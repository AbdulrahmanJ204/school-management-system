<?php

namespace App\Services\Files;

use App\Enums\StringsManager\Files\FileStr;
use App\Enums\UserType;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Requests\File\UpdateFileRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\FileTarget;
use App\Models\Subject;
use App\Models\TeacherSectionSubject;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

trait UpdateFile
{


    /**
     * @throws PermissionException
     */
    public function update(UpdateFileRequest $request, File $file): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $userType = $user->user_type;
        if ($userType == UserType::Teacher->value)
            $this->authorizeTeacherForUpdate(
                request: $request,
                file: $file,
                data: $data,
                user: $user
            );
        // if request have subject id -> then update to the new subject id
        // if I want to make this file global , maybe entered subject id by wrong -> send is_general in body .
        //
        //
        // algorithm summary
        // if title|description changed -> no effect on other data
        // if file changed -> effect on size, path , file stored in storage.
        // if subject id changed -> file should be renamed and moved to another directory
        // what about targets???
        // these are handled alone using section_ids and grade_ids , no affect of the previous data on it
        // file change was handled , I should handle section change.

        $updateData = [];
        // Handle Title and Description change
        if ($request->filled('title')) {
            $updateData['title'] = $data['title'];
        }
        if ($request->filled('type')) {
            $updateData['type'] = $data['type'];
        }
        if ($request->has('description')) {
            $updateData['description'] = $data['description'];
        }

        // handle subject change , to send new code to handle file changes in case of any change.
        // if user sent subject id and no_subject parameters , subject_id has higher priority
        $requestHasFile = $request->hasFile('file');
        $requestChangedSubject =
            $request->has('subject_id') &&
            $data['subject_id'] !== $file->subject_id;

        $subjectCode = $file->subject_id ?
            Subject::find($file->subject_id)->code :
            $this->generalPath;
        if ($requestChangedSubject) {
            $subjectCode = Subject::find($data['subject_id'])?->code ?? $this->generalPath;
            $updateData['subject_id'] = $data['subject_id'];
        }
        if (
            $request->filled('no_subject')
            &&
            $file->subject_id
        ) {
            $updateData['subject_id'] = null;
            $requestChangedSubject = true;
            $subjectCode = $this->generalPath;
        }

        $filePath = null;
        if ($requestHasFile) {
            $filePath = $this->handleFile($request, $subjectCode, $file->file);
        } else if ($requestChangedSubject) {
            $filePath = $this->moveFile($file, $subjectCode);
        }

        if ($filePath) {
            $updateData['file'] = $filePath;
            $updateData['size'] = Storage::disk($this->storageDisk)->size($filePath);
        }

        if ($userType == UserType::Admin->value) {
            $this->adminUpdateTargets(
                request: $request,
                data: $data,
                model: $file,
                targetsClass: FileTarget::class,
            );
        } else if ($request->filled('section_ids')) {
            $this->handleFileTargetsOnUpdateTeacher(
                data: $data,
                file: $file,
                user: $user);
        }

        $file->update($updateData);
        $file->loadSectionAndGrade();
        return ResponseHelper::jsonResponse(FileResource::make($file), __(FileStr::messageUpdated->value));
    }

    private function authorizeTeacherForUpdate(UpdateFileRequest $request, File $file, $data, $user): void
    {


        $teacher = $user->teacher;
        $fileSections = $file->targets()->pluck('section_id')->toArray();

        $teacherOwnsFile =
            TeacherSectionSubject::where('teacher_id', $teacher->id)
                ->where('is_active', true)
                ->where('subject_id', $file->subject_id)
                ->whereIn('section_id', $fileSections)->exists();


        if (!$teacherOwnsFile) {
            throw new PermissionException();
        }

        $fileBelongsToOneTeacher = $file->belongsToOneTeacher();

        // get teacher valid subject_section records
        // if the file targeted one of them
        // and
        // teacher modified the subject and section to one of the records
        // then he can update the file.


        if ($request->filled('subject_id')) {
            if (!$fileBelongsToOneTeacher) {
                throw new PermissionException();
            }
            $newSubjectId = $data['subject_id'];
            $teacherSections =
                TeacherSectionSubject::where('teacher_id', $teacher->id)
                    ->where('is_active', true)
                    ->where('subject_id', $newSubjectId)
                    ->pluck('section_id')->toArray();

            $targetsSections = $data['section_ids'];
            $canTarget = array_intersect($teacherSections, $targetsSections);
            $cannotTarget = array_diff($targetsSections, $canTarget);
            if (empty($canTarget) || !empty($cannotTarget)) {
                throw new PermissionException();
            }
        } else if ($request->filled('section_ids')) {
            $teacherSections =

                TeacherSectionSubject::where('teacher_id', $teacher->id)
                    ->where('is_active', true)
                    ->where('subject_id', $file->subject_id)
                    ->pluck('section_id')->toArray();
            $targetsSections = $data['section_ids'];

            $canTarget = array_intersect($teacherSections, $targetsSections);
            $cannotTarget = array_diff($targetsSections, $canTarget);
            if (empty($canTarget) || !empty($cannotTarget)) {
                throw new PermissionException();
            }
        }


    }

    private function handleFileTargetsOnUpdateTeacher(mixed $data, File $file, $user): void
    {
        // Handle when an admin published a file for sections of two teachers
        // if i used the admin logic ,
        // it will delete all previous inserted sections, including the second teacher sections
        // so i would not delete all previous non-existing records in the section_ids
        // just delete the ones that are in diff(teacherSections, requestSections).
        // another thing , in previous updates we deleted grade and general targets ,
        // no need here , teacher would update only helper files that their targets are sections
        //and would edit them to be sections also , so it would be only updateTargetsTeacher
        // that includes deleting teacher non required targets (sections), and adding new ones


        $teacher = $user->teacher;

        $teacherSections =
            TeacherSectionSubject::where('teacher_id', $teacher->id)
                ->where('is_active', true)
                ->where('subject_id', $file->subject_id)
                ->pluck('section_id')->toArray();


        $existingSections = $file->targets()
            ->whereIn('section_id', $teacherSections)
            ->pluck('section_id')
            ->toArray();

        $sectionsToDelete = array_diff($existingSections, $data['section_ids']);
        $sectionsToAdd = array_diff($data['section_ids'], $existingSections);

        $file->targets()->whereIn('section_id', $sectionsToDelete)
            ->whereNull('grade_id')
            ->delete();
        foreach ($sectionsToAdd as $section_id) {
            FileTarget::create([
                'file_id' => $file->id,
                'grade_id' => null,
                'section_id' => $section_id,
                'created_by' => $user->id,
            ]);
        }
    }
}
