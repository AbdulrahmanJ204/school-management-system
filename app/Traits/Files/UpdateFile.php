<?php

namespace App\Traits\Files;

use App\Enums\StringsManager\FileStr;
use App\Enums\UserType;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Requests\file\UpdateFileRequest;
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
        $userType = $user->user_type;
        return match ($userType) {
            UserType::Admin->value => $this->adminUpdate($request, $file),
            UserType::Teacher->value => $this->teacherUpdate($request, $file),
            default => throw new PermissionException()
        };
    }

    public function adminUpdate(UpdateFileRequest $request, File $file): JsonResponse
    {
        $data = $request->validated();
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
        if ($request->filled($this->apiTitle)) {
            $updateData['title'] = $data[$this->apiTitle];
        }
        if ($request->filled($this->apiType)) {
            $updateData['type'] = $data[$this->apiType];
        }
        if ($request->has($this->apiDescription)) {
            $updateData['description'] = $data[$this->apiDescription];
        }

        // handle subject change , to send new code to handle file changes in case of any change.
        // if user sent subject id and no_subject parameters , subject_id has higher priority
        $requestHasFile = $request->hasFile($this->apiFile);
        $requestChangedSubject =
            $request->has($this->apiSubjectId) &&
            $data[$this->apiSubjectId] !== $file->subject_id;

        $subjectCode = $file->subject_id ?
            Subject::find($file->subject_id)->code :
            $this->generalPath;
        if ($requestChangedSubject) {
            $subjectCode = Subject::find($data[$this->apiSubjectId])->code;
            $updateData['subject_id'] = $data[$this->apiSubjectId];
        }
        if (
            $request->filled($this->apiNoSubject)
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

        $this->handleFileTargetsOnUpdate($data, $request, $file);
        $file->update($updateData);
        $file->loadSectionAndGrade();
        return ResponseHelper::jsonResponse(FileResource::make($file), __(FileStr::messageUpdated->value));
    }

    private function teacherUpdate(UpdateFileRequest $request, File $file): JsonResponse
    {

        $data = $request->validated();
        $teacher = $request->user()->teacher;


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


        if ($request->filled($this->apiSubjectId)) {
            if (!$fileBelongsToOneTeacher) {
                throw new PermissionException();
            }
            $newSubjectId = $data[$this->apiSubjectId];
            $teacherSections =
                TeacherSectionSubject::where('teacher_id', $teacher->id)
                    ->where('is_active', true)
                    ->where('subject_id', $newSubjectId)
                    ->pluck('section_id')->toArray();

            $targetsSections = $data[$this->apiSectionIds];
            $canTarget = array_intersect($teacherSections, $targetsSections);
            $cannotTarget = array_diff($targetsSections, $canTarget);
            if (empty($canTarget) || !empty($cannotTarget)) {
                throw new PermissionException();
            }
        } else if ($request->filled($this->apiSectionIds)) {
            $teacherSections =

                TeacherSectionSubject::where('teacher_id', $teacher->id)
                    ->where('is_active', true)
                    ->where('subject_id', $file->subject_id)
                    ->pluck('section_id')->toArray();
            $targetsSections = $data[$this->apiSectionIds];

            $canTarget = array_intersect($teacherSections, $targetsSections);
            $cannotTarget = array_diff($targetsSections, $canTarget);
            if (empty($canTarget) || !empty($cannotTarget)) {
                throw new PermissionException();
            }
        }


        $updateData = [];
        // Handle Title and Description change
        if ($request->filled($this->apiTitle)) {
            $updateData['title'] = $data[$this->apiTitle];
        }

        if ($request->has($this->apiDescription)) {
            $updateData['description'] = $data[$this->apiDescription];
        }

        // handle subject change , to send new code to handle file changes in case of any change.
        // if user sent subject id and no_subject parameters , subject_id has higher priority
        $requestHasFile = $request->hasFile($this->apiFile);
        $requestChangedSubject =
            $request->filled($this->apiSubjectId) &&
            $data[$this->apiSubjectId] !== $file->subject_id;

        $subjectCode = Subject::find($file->subject_id)->code;
        if ($requestChangedSubject) {
            $subjectCode = Subject::find($data[$this->apiSubjectId])->code;
            $updateData['subject_id'] = $data[$this->apiSubjectId];
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

        if ($request->filled($this->apiSectionIds)) {
            $this->handleFileTargetsOnUpdateTeacher($data, $request, $file);
        }
        $file->update($updateData);
        $file->loadSectionAndGrade();
        return ResponseHelper::jsonResponse(FileResource::make($file), __(FileStr::messageUpdated->value));

    }

    private function handleFileTargetsOnUpdateTeacher(mixed $data, UpdateFileRequest $request, File $file)
    {
        // Handle when an admin published a file for sections of two teachers
        // if i used the admin logic ,
        // it will delete all previous inserted sections, including the second teacher sections
        // so i would not delete all previous non existing records in the section_ids
        // just delete the ones that are in diff(teacherSections, requestSections).
        // another thing , in previous updates we deleted grade and general targets ,
        // no need here , teacher would update only helper files that their targets are sections
        //and would edit them to be sections also , so it would be only updateTargetsTeacher
        // that includes deleting teacher non required targets (sections), and adding new ones

        $user = $request->user();
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

        $sectionsToDelete = array_diff($existingSections, $data[$this->apiSectionIds]);
        $sectionsToAdd = array_diff($data[$this->apiSectionIds], $existingSections);

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

    private function handleFileTargetsOnUpdate($data, $request, File $file): void
    {
        $user = auth()->user();
        if ($request->filled($this->apiSectionIds)) {
            $this->updateSections($file, $data);
        } else if ($request->filled($this->apiGradeIds)) {
            $this->updateGrades($file, $data);
        } else if ($request->filled($this->apiIsGeneral) && $data[$this->apiIsGeneral]) {
            $alreadyGeneral = $file
                ->targets()
                ->whereNull('section_id')
                ->whereNull('grade_id')
                ->exists();
            if ($alreadyGeneral) {
                return;
            }
            $file->targets()->delete();
            FileTarget::create([
                'file_id' => $file->id,
                'grade_id' => null,
                'section_id' => null,
                'created_by' => $user->id,
            ]);
        }
    }

    private function updateSections($file, $data): void
    {
        $user = auth()->user();
        $file->targets()->whereNotNull('grade_id')->delete();
        $file->targets()->whereNull('section_id')->whereNull('grade_id')->delete();

        $existingSections = $file->targets()
            ->whereNotNull('section_id')
            ->whereNull('grade_id')
            ->pluck('section_id')
            ->toArray();


        $sectionsToDelete = array_diff($existingSections, $data[$this->apiSectionIds]);
        $sectionsToAdd = array_diff($data[$this->apiSectionIds], $existingSections);
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

    private function updateGrades($file, $data): void
    {
        $user = auth()->user();
        $file->targets()->whereNotNull('section_id')->delete();
        $file->targets()->whereNull('section_id')->whereNull('grade_id')->delete();
        $existingGrades = $file->targets()
            ->whereNull('section_id')
            ->whereNotNull('grade_id')
            ->pluck('grade_id')
            ->toArray();

        $gradesToDelete = array_diff($existingGrades, $data[$this->apiGradeIds]);
        $gradesToAdd = array_diff($data[$this->apiGradeIds], $existingGrades);
        $file->targets()
            ->whereIn('grade_id', $gradesToDelete)
            ->whereNull('section_id')
            ->delete();

        foreach ($gradesToAdd as $grade_id) {
            FileTarget::create([
                'file_id' => $file->id,
                'grade_id' => $grade_id,
                'section_id' => null,
                'created_by' => $user->id,
            ]);
        }
    }


}
