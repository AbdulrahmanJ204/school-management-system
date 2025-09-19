<?php

namespace App\Http\Resources;

use App\Enums\Permissions\FilesPermission;
use App\Enums\StringsManager\Files\FileApi;
use App\Enums\UserType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user_type = Auth::user()->user_type;
        $array = $this->studentArray();

        if ($user_type === UserType::Admin->value) {
            $array = array_merge($array, $this->adminAdditionalAttributes());

        }
        if ($user_type === UserType::Teacher->value) {
            $array = array_merge($array, $this->teacherAdditionalAttributes());

        }
        return $array;
    }

    /**
     * @return array
     */
    public function studentArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->title,
            "description" => $this->description,
            "type" => $this->type,
            "size" => $this->size,
            "publish date" => $this->publish_date->format('Y-m-d h:i:s A'),
            'subject' => $this->subject ?? null
        ];
    }

    /**
     * @param array $array
     * @return array
     */
    public function adminAdditionalAttributes(): array
    {


        $targets = $this->whenLoaded('targets');
        $grades = FileGradeResource::collection($targets->whereNotNull('grade')->pluck('grade')->unique()->values());
        $sections = FileSectionResource::collection($targets->whereNotNull('section')->pluck('section')->unique()->values());

        $targetsArray = [];

        if ($sections->isNotEmpty()) {
            $targetsArray['sections'] = $sections;
        }
        if ($grades->isNotEmpty()) {
            $targetsArray['grades'] = $grades;
        }

        return [
            'download_count'=>$this->downloadsCount(),
            'deleted_at' => $this->deleted_at?->format('Y-m-d h:i:s A'),
            'targets' => $targetsArray
        ];
    }

    private function teacherAdditionalAttributes(): array
    {
        $targets = $this->whenLoaded('targets');
        $targets->whereNotNull('section')->pluck('section')->unique()->values();

            $sections = FileSectionResource::collection($targets->whereNotNull('section')->pluck('section')->unique()->values());
        $grade = $sections[0]->grade ?? 'غير محدد';

        $deleted = $this->deleted_at?->format('Y-m-d h:i:s A');
        $canDel = Auth::user()->hasPermissionTo(FilesPermission::softDelete->value)
            && $this->belongsToOneTeacher()
            && !$deleted;
        return [
            'grade' => $grade,
            'sections' => $sections,
            FileApi::apiCanDelete->value => $canDel,
        ];
    }
}
