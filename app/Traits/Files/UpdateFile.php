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
            $updateData[$this->dbTitle] = $data[$this->apiTitle];
        }
        if ($request->filled($this->apiType)) {
            $updateData[$this->dbType] = $data[$this->apiType];
        }
        if ($request->has($this->apiDescription)) {
            $updateData[$this->dbDescription] = $data[$this->apiDescription];
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
            $updateData[$this->dbSubjectId] = $data[$this->apiSubjectId];
        }
        if (
            $request->filled($this->apiNoSubject)
            &&
            $file->subject_id
        ) {
            $updateData[$this->dbSubjectId] = null;
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
            $updateData[$this->dbFile] = $filePath;
            $updateData[$this->dbSize] = Storage::disk($this->storageDisk)->size($filePath);
        }

        $this->handleFileTargetsOnUpdate($data, $request, $file);
        $file->update($updateData);
        $file->loadSectionAndGrade();
        return ResponseHelper::jsonResponse(FileResource::make($file), __(FileStr::messageUpdated->value));
    }

    private function teacherUpdate(UpdateFileRequest $request, File $file): JsonResponse
    {
        // TODO : implement this
        $request->validated();
        return ResponseHelper::jsonResponse(FileResource::make($file), __(FileStr::messageUpdated->value));
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
                ->whereNull($this->dbSectionId  )
                ->whereNull($this->dbGradeId)
                ->exists();
            if ($alreadyGeneral) {
                return;
            }
            $file->targets()->delete();
            FileTarget::create([
                $this->dbFileId => $file->id,
                $this->dbGradeId => null,
                $this->dbSectionId => null,
                $this->dbCreatedBy => $user->id,
            ]);
        }
    }

    private function updateSections($file, $data): void
    {
        $user = auth()->user();
        $file->targets()->whereNotNull($this->dbGradeId)->delete();
        $file->targets()->whereNull($this->dbSectionId)->whereNull($this->dbGradeId)->delete();

        $existingSections = $file->targets()
            ->whereNotNull($this->dbSectionId)
            ->whereNull($this->dbGradeId)
            ->pluck($this->dbSectionId)
            ->toArray();


        $sectionsToDelete = array_diff($existingSections, $data[$this->apiSectionIds]);
        $sectionsToAdd = array_diff($data[$this->apiSectionIds], $existingSections);
        $file->targets()->whereIn($this->dbSectionId, $sectionsToDelete)
            ->whereNull($this->dbGradeId)
            ->delete();
        foreach ($sectionsToAdd as $section_id) {
            FileTarget::create([
                $this->dbFileId => $file->id,
                $this->dbGradeId => null,
                $this->dbSectionId => $section_id,
                $this->dbCreatedBy => $user->id,
            ]);
        }
    }

    private function updateGrades($file, $data): void
    {
        $user = auth()->user();
        $file->targets()->whereNotNull($this->dbSectionId)->delete();
        $file->targets()->whereNull($this->dbSectionId)->whereNull($this->dbGradeId)->delete();
        $existingGrades = $file->targets()
            ->whereNull($this->dbSectionId)
            ->whereNotNull($this->dbGradeId)
            ->pluck($this->dbGradeId)
            ->toArray();

        $gradesToDelete = array_diff($existingGrades, $data[$this->apiGradeIds]);
        $gradesToAdd = array_diff($data[$this->apiGradeIds], $existingGrades);
        $file->targets()
            ->whereIn($this->dbGradeId, $gradesToDelete)
            ->whereNull($this->dbSectionId)
            ->delete();

        foreach ($gradesToAdd as $grade_id) {
            FileTarget::create([
                $this->dbFileId => $file->id,
                $this->dbGradeId => $grade_id,
                $this->dbSectionId => null,
                $this->dbCreatedBy => $user->id,
            ]);
        }
    }


}
