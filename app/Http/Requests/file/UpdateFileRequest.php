<?php

namespace App\Http\Requests\File;

use App\Enums\FileType;
use App\Enums\Permissions\FilesPermission;
use App\Enums\StringsManager\Files\FileApi;
use App\Enums\UserType;
use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UpdateFileRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo(FilesPermission::update->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $userType = auth()->user()->user_type;
        return match ($userType) {
            UserType::Admin->value => $this->adminRules(),
            UserType::Teacher->value => $this->teacherRules(),

        };
    }

    /**
     * @return array
     */
    public function adminRules(): array
    {
        return [
            FileApi::apiSubjectId->value => 'sometimes|nullable|exists:subjects,id',
            FileApi::apiTitle->value => 'sometimes|string|max:255',
            FileApi::apiDescription->value => 'sometimes|nullable|string',
            FileApi::apiFile->value => 'sometimes|file',
            FileApi::apiType->value => ['sometimes', Rule::enum(FileType::class)],
            FileApi::apiSectionIds->value => 'sometimes|array',
            FileApi::apiSectionIds->value . '.*' => 'exists:sections,id',
            FileApi::apiGradeIds->value => ['array', 'missing_with:' . FileApi::apiSectionIds->value],
            FileApi::apiGradeIds->value . '.*' => 'exists:grades,id',
            FileApi::apiIsGeneral->value => ['boolean','missing_with:'.FileApi::apiGradeIds->value.','.FileApi::apiSectionIds->value],
            FileApi::apiNoSubject->value => ['boolean','missing_with:'.FileApi::apiSubjectId->value],
        ];
    }

    private function teacherRules(): array
    {
        return [
            FileApi::apiSubjectId->value => 'sometimes|exists:subjects,id',
            FileApi::apiTitle->value => 'sometimes|string|max:255',
            FileApi::apiDescription->value => 'sometimes|nullable|string',
            FileApi::apiFile->value => 'sometimes|file',
            FileApi::apiSectionIds->value => ['array', 'required_with:' . FileApi::apiSubjectId->value],
            FileApi::apiSectionIds->value . '.*' => 'exists:sections,id',

            FileApi::apiType->value => 'missing',
            FileApi::apiGradeIds->value => 'missing',
            FileApi::apiIsGeneral->value => 'missing',
            FileApi::apiNoSubject->value => 'missing',
        ];
    }
}
