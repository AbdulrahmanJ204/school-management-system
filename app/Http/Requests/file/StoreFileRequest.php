<?php

namespace App\Http\Requests\File;

use App\Enums\FileType;
use App\Enums\Permissions\FilesPermission;
use App\Enums\StringsManager\Files\FileApi;
use App\Enums\UserType;
use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StoreFileRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->hasPermissionTo(FilesPermission::store->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $userType = Auth::user()->user_type;
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
            FileApi::apiTitle->value => 'required|string|max:255',
            FileApi::apiDescription->value => 'sometimes|nullable|string',
            FileApi::apiFile->value => 'required|file',
            FileApi::apiType->value => ['required', Rule::enum(FileType::class)],
            FileApi::apiSectionIds->value => 'sometimes|array',
            FileApi::apiSectionIds->value . '.*' => 'exists:sections,id',
            FileApi::apiGradeIds->value => ['missing_with:' . FileApi::apiSectionIds->value, 'array'],
            FileApi::apiGradeIds->value . '.*' => 'exists:grades,id',
        ];
    }

    private function teacherRules(): array
    {
        return [
            FileApi::apiSubjectId->value => 'required|exists:subjects,id',
            FileApi::apiTitle->value => 'required|string|max:255',
            FileApi::apiDescription->value => 'sometimes|nullable|string',
            FileApi::apiFile->value => 'required|file',
            FileApi::apiSectionIds->value => 'required|array',
            FileApi::apiSectionIds->value . '.*' => 'exists:sections,id',

            FileApi::apiType->value => 'missing',
            FileApi::apiGradeIds->value => 'missing',
        ];
    }
}
