<?php

namespace App\Services\Files;

use App\Enums\StringsManager\FileStr;
use App\Enums\UserType;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Requests\File\StoreFileRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\FileTarget;
use App\Models\NewsTarget;
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
        // TODO :Extra validation on match of subject with grade
        $user = $request->user();
        $user_type = $user->user_type;
        $data = $request->validated();
        if($user_type===UserType::Teacher->value)
            $this->authorizeTeacherForCreate(data: $data , user: $user);
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
        $this->handleTargetsOnCreate(
            request: $request,
            data: $data,
            model: $result,
            targetsClass: FileTarget::class
        );
        $result->loadSectionAndGrade();
        return ResponseHelper::jsonResponse(FileResource::make($result), __(FileStr::messageStored->value));
    }


    private function authorizeTeacherForCreate($data , $user): void
    {
        // Teacher Can publish Files to the section_subject he teaches
        $teacher = $user->teacher;
        $teacherTeachesAllTargets =
            TeacherSectionSubject::where('teacher_id', $teacher->id)
                ->where('subject_id', $data[$this->apiSubjectId])
                ->whereIn('section_id', $data[$this->apiSectionIds])
                ->where('is_active', true)
                ->exists();
        if (!$teacherTeachesAllTargets) {
            throw new PermissionException();
        }

    }

}
