<?php

namespace App\Http\Requests\News;

use App\Enums\Permissions\NewsPermission;
use App\Enums\StringsManager\NewsStr;
use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class StoreNewsRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->hasPermissionTo(NewsPermission::create->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            NewsStr::apiTitle->value => 'required|string|max:255',
            NewsStr::apiContent->value => 'required|string',
            NewsStr::apiPhoto->value => 'sometimes|image|max:4096',
            NewsStr::apiSectionIds->value => 'array',
            NewsStr::apiSectionIds->value . '.*' => 'exists:sections,id',
            NewsStr::apiGradeIds->value => [
                'array',
                'missing_with:' . NewsStr::apiSectionIds->value,
            ],
            NewsStr::apiGradeIds->value . '.*' => 'exists:grades,id',
        ];
    }
}
