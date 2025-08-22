<?php

namespace App\Http\Requests\File;

use App\Enums\FileType;
use App\Enums\StringsManager\QueryParams;
use App\Enums\UserType;
use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ListFilesRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $usertype = Auth::user()->user_type;
        return match ($usertype) {
            UserType::Admin->value => $this->adminRules(),
            UserType::Teacher->value => $this->teacherRules(),
            UserType::Student->value => $this->studentRules(),
        };

    }

    private function teacherRules(): array
    {
        return [
            QueryParams::Subject->value => 'sometimes|nullable|exists:subjects,id',
            QueryParams::Section->value => 'sometimes|exists:sections,id',
            QueryParams::Year->value => 'missing',
            QueryParams::Type->value => 'missing',
            QueryParams::Grade->value => 'missing',
            QueryParams::General->value => 'missing'
        ];
    }

    private function adminRules(): array
    {
        return [
            QueryParams::Year->value => 'sometimes|exists:years,id',
            QueryParams::Subject->value => 'sometimes|nullable|exists:subjects,id',
            QueryParams::Type->value => ['sometimes', Rule::enum(FileType::class)],
            QueryParams::Section->value => 'sometimes|exists:sections,id',
            QueryParams::Grade->value => 'sometimes|exists:grades,id',
            QueryParams::General->value => 'sometimes'
            ,
        ];
    }

    private function studentRules(): array
    {
        return [
            QueryParams::Year->value => 'sometimes|exists:years,id',
            QueryParams::Subject->value => 'sometimes|nullable|exists:subjects,id',
            QueryParams::Type->value => ['sometimes', Rule::enum(FileType::class)],

        ];
    }
}
