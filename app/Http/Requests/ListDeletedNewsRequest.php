<?php

namespace App\Http\Requests;

use App\Enums\Permissions\NewsPermission;
use App\Enums\StringsManager\QueryParams;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ListDeletedNewsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->hasPermissionTo(NewsPermission::ListDeletedNews->value);

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
            QueryParams::Section->value => 'sometimes|exists:sections,id',
            QueryParams::Grade->value => 'sometimes|exists:grades,id',
            QueryParams::General->value => 'sometimes'
        ];

    }
}
