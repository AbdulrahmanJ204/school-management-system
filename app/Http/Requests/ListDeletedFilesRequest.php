<?php

namespace App\Http\Requests;

use App\Enums\FileType;
use App\Enums\Permissions\FilesPermission;
use App\Enums\StringsManager\QueryParams;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ListDeletedFilesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
        // TODO :
        //return Auth::user()->hasPermissionTo(FilesPermission::listDeleted->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
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
}
