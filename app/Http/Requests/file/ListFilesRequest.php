<?php

namespace App\Http\Requests\File;

use App\Enums\FileType;
use App\Enums\StringsManager\FileStr;
use App\Http\Requests\BaseRequest;
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            FileStr::queryYear->value =>'sometimes|exists:years,id',
            FileStr::querySubject->value=>'sometimes|nullable|exists:subjects,id',
            FileStr::apiType->value=>['sometimes' , Rule::enum(FileType::class)],
        ];
    }
}
