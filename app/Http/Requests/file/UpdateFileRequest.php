<?php

namespace App\Http\Requests\file;

use App\Enums\FileType;
use App\Enums\Permissions\FilesPermission;
use App\Enums\StringsManager\FileStr;
use App\Enums\UserType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateFileRequest extends FormRequest
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
            FileStr::apiSubjectId->value => 'sometimes|nullable|exists:subjects,id',
            FileStr::apiTitle->value => 'sometimes|string|max:255',
            FileStr::apiDescription->value => 'sometimes|nullable|string',
            FileStr::apiFile->value => 'sometimes|file',
            FileStr::apiType->value => ['sometimes', Rule::enum(FileType::class)],
            FileStr::apiSectionIds->value => 'sometimes|array',
            FileStr::apiSectionIds->value . '.*' => 'exists:sections,id',
            FileStr::apiGradeIds->value => ['array', 'missing_with:' . FileStr::apiSectionIds->value],
            FileStr::apiGradeIds->value . '.*' => 'exists:grades,id',
            FileStr::apiIsGeneral->value => ['boolean','missing_with:'.FileStr::apiGradeIds->value.','.FileStr::apiSectionIds->value],
            FileStr::apiNoSubject->value => ['boolean','missing_with:'.FileStr::apiSubjectId->value],
        ];
    }

    private function teacherRules(): array
    {
        return [
            FileStr::apiSubjectId->value => 'sometimes|exists:subjects,id',
            FileStr::apiTitle->value => 'sometimes|string|max:255',
            FileStr::apiDescription->value => 'sometimes|nullable|string',
            FileStr::apiFile->value => 'sometimes|file',
            FileStr::apiSectionIds->value => ['array', 'required_with:' . FileStr::apiSubjectId->value],
            FileStr::apiSectionIds->value . '.*' => 'exists:sections,id',

            FileStr::apiType->value => 'missing',
            FileStr::apiGradeIds->value => 'missing',
            FileStr::apiIsGeneral->value => 'missing',
            FileStr::apiNoSubject->value => 'missing',
        ];
    }
}
