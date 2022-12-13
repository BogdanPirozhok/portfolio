<?php

namespace App\Http\Requests\Cabinet;

use App\Models\Cabinet\CabinetDomain;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CabinetStoreDomainRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'type_id' => [
                'required',
                'numeric',
                Rule::in(CabinetDomain::DOMAIN_TYPES)
            ],
            'hostname' => [
                'required',
                'string',
                'regex:/^(?!\-)(?:(?:[a-zA-Z\d][a-zA-Z\d\-]{0,61})?[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/i',
                Rule::unique('cabinet_domains', 'hostname')->whereNull('deleted_at')
            ]
        ];
    }
}
