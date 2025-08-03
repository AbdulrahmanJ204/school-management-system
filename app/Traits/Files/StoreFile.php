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
use App\Models\TeacherSectionSubject;
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
            'subject_id' => $data[$this->apiSubjectId],
            'title' => $data[$this->apiTitle],
            'description' => $data[$this->apiDescription],
            'publish_date' => $publish_date,
            'file' => $file,
            'size' => $size,
            'created_by' => $request->user()->id,
        ];
        if ($request->filled($this->apiType)) {
            $array['type'] = $data[$this->apiType];
        }
        $result = File::create($array);
        $this->handleFileTargetsOnCreate($result, $request, $data);
        $result->loadSectionAndGrade();
        return ResponseHelper::jsonResponse(FileResource::make($result), __(FileStr::messageStored->value));
    }

    private function teacherStore(StoreFileRequest $request): JsonResponse
    {

        $data = $request->validated();
        // Teacher Can publish Files to the section_subject he teaches
        $teacher = $request->user()->teacher;
        $teacherTeachesTarget  =
            TeacherSectionSubject::where('teacher_id', $teacher->id)
            ->where('subject_id', $data[$this->apiSubjectId])
            ->whereIn('section_id',$data[$this->apiSectionIds])
            ->where('is_active' , true)
            ->exists();
        if(!$teacherTeachesTarget) {
            throw new PermissionException();
        }
        $publish_date = now();
        $subjectCode = Subject::find($data[$this->apiSubjectId])?->code ?? $this->generalPath;
        $file = $this->handleFile($request, $subjectCode);
        $size = Storage::disk($this->storageDisk)->size($file);
        $array = [
            'subject_id' => $data[$this->apiSubjectId],
            'title' => $data[$this->apiTitle],
            'description' => $data[$this->apiDescription],
            'publish_date' => $publish_date,
            'type' => 'helper',
            'file' => $file,
            'size' => $size,
            'created_by' => $request->user()->id,
        ];
        $result = File::create($array);
        $this->handleFileTargetsOnCreate($result, $request, $data);
        $result->loadSectionAndGrade();
        return ResponseHelper::jsonResponse(FileResource::make($result), __(FileStr::messageStored->value));
    }

    private function handleFileTargetsOnCreate(File $file, $request, $data): void
    {
        $user = auth()->user();
        if ($request->filled($this->apiSectionIds)) {
            foreach ($data[$this->apiSectionIds] as $section_id) {
                FileTarget::create([
                    'file_id' => $file->id,
                    'grade_id' => null,
                    'section_id' => $section_id,
                    'created_by' => $user->id,
                ]);
            }
        } else if ($request->filled($this->apiGradeIds)) {
            foreach ($data[$this->apiGradeIds] as $grade_id) {
                FileTarget::create([
                    'file_id' => $file->id,
                    'grade_id' => $grade_id,
                    'section_id' => null,
                    'created_by' => $user->id,
                ]);
            }
        } else {
            // Target all users
            FileTarget::create([
                'file_id' => $file->id,
                'grade_id' => null,
                'section_id' => null,
                'created_by' => $user->id,
            ]);
        }
    }

}
