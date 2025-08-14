<?php

namespace App\Http\Requests\News;

use App\Enums\Permissions\NewsPermission;
use App\Enums\StringsManager\NewsStr;
use App\Http\Requests\BaseRequest;

class UpdateNewsRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can(NewsPermission::update->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            NewsStr::apiTitle->value => 'sometimes|string|max:255',
            NewsStr::apiContent->value => 'sometimes|string',
            NewsStr::apiPhoto->value => 'sometimes|image|max:4096',
            NewsStr::apiRemovePhoto->value => [
                'missing_with:' . NewsStr::apiPhoto->value,
                'boolean'
            ],
            NewsStr::apiSectionIds->value => 'sometimes|array',
            NewsStr::apiSectionIds->value . '.*' => 'exists:sections,id',
            NewsStr::apiGradeIds->value => [
                'missing_with:' . NewsStr::apiSectionIds->value,
                'array'
            ],
            NewsStr::apiGradeIds->value . '.*' => 'exists:grades,id',
            NewsStr::apiIsGeneral->value => [
                'missing_with:' .
                NewsStr::apiSectionIds->value . ',' . NewsStr::apiGradeIds->value,
                'boolean'
            ],
        ];
    }
}
