<?php

namespace App\Http\Requests\Cabinet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CabinetStoreSiteRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'min:4',
                'max:255'
            ],
            'slug' => [
                'required',
                'string',
                'min:4',
                'max:255',
                'regex:/^[a-zA-Z0-9]+[a-zA-Z0-9\-]+[a-zA-Z0-9]+$/',
                'not_regex:/\-{2,}/',
                Rule::unique('cabinet_sites', 'slug')->whereNull('deleted_at')
            ],
        ];
    }

    public function attributes(): array
    {
        return array_merge(parent::attributes(), [
            'slug' => lang('validation.attributes.subdomain'),
            'name' => lang('validation.attributes.cabinet_name'),
        ]);
    }
}
