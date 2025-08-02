<?php

namespace App\Http\Requests\file;

use App\Enums\FileType;
use App\Enums\Permissions\FilesPermission;
use App\Enums\StringsManager\FileStr;
use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class StoreFileRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo(FilesPermission::store->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            FileStr::apiSubjectId->value => 'sometimes|nullable|exists:subjects,id',
            FileStr::apiTitle->value => 'required|string|max:255',
            FileStr::apiDescription->value => 'sometimes|nullable|string',
            FileStr::apiFile->value => 'required|file',
            FileStr::apiType->value => ['sometimes', Rule::enum(FileType::class)],
            FileStr::apiSectionIds->value => 'sometimes|array',
            FileStr::apiSectionIds->value . '.*' => 'exists:sections,id',
            FileStr::apiGradeIds->value => 'sometimes|array',
            FileStr::apiGradeIds->value . '.*' => 'exists:grades,id',
        ];
    }
}
