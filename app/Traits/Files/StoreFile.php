<?php

namespace App\Traits\Files;

use App\Enums\StringsManager\FileStr;
use App\Enums\UserType;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Requests\file\StoreFileRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\FileTarget;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

trait StoreFile
{
    /**
     * @throws PermissionException
     */
    public function store(StoreFileRequest $request): JsonResponse
    {
        $user_type = $request->user()->user_type;
        return match ($user_type) {
            UserType::Admin->value => $this->adminStore($request),
            UserType::Teacher->value => $this->teacherStore($request),
            default => throw new PermissionException(),
        };
    }

    public function adminStore(StoreFileRequest $request): JsonResponse
    {
        $data = $request->validated();

        $publish_date = now();
        $subjectCode = Subject::find($data[$this->apiSubjectId])?->code ?? $this->generalPath;
        $file = $this->handleFile($request, $subjectCode);
        $size = Storage::disk($this->storageDisk)->size($file);
        $array = [
            $this->dbSubjectId => $data[$this->apiSubjectId],
            $this->dbTitle => $data[$this->apiTitle],
            $this->dbDescription => $data[$this->apiDescription],
            $this->dbPublishDate => $publish_date,
            $this->dbFile => $file,
            $this->dbSize => $size,
            $this->dbCreatedBy => $request->user()->id,
        ];
        if ($request->filled($this->apiType)) {
            $array[$this->dbType] = $data[$this->apiType];
        }
        $result = File::create($array);
        $this->handleFileTargetsOnCreate($result, $request, $data);
        $result->loadSectionAndGrade();
        return ResponseHelper::jsonResponse(FileResource::make($result), __(FileStr::messageStored->value));
    }

    private function teacherStore(StoreFileRequest $request): JsonResponse
    {
        // TODO: Implement this
        $request->validated();
        return ResponseHelper::jsonResponse([], __(FileStr::messageStored->value));

    }

    private function handleFileTargetsOnCreate(File $file, $request, $data): void
    {
        $user = auth()->user();
        if ($request->filled($this->apiSectionIds)) {
            foreach ($data[$this->apiSectionIds] as $section_id) {
                FileTarget::create([
                    $this->dbFileId => $file->id,
                    $this->dbGradeId => null,
                    $this->dbSectionId => $section_id,
                    $this->dbCreatedBy => $user->id,
                ]);
            }
        } else if ($request->filled($this->apiGradeIds)) {
            foreach ($data[$this->apiGradeIds] as $grade_id) {
                FileTarget::create([
                    $this->dbFileId => $file->id,
                    $this->dbGradeId => $grade_id,
                    $this->dbSectionId => null,
                    $this->dbCreatedBy => $user->id,
                ]);
            }
        } else {
            // Target all users
            FileTarget::create([
                $this->dbFileId => $file->id,
                $this->dbGradeId => null,
                $this->dbSectionId => null,
                $this->dbCreatedBy => $user->id,
            ]);
        }
    }

}
