<?php

namespace App\Http\Requests\Cabinet;

use App\Models\Cabinet\CabinetDomain;
use App\Rules\CabinetMembership;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CabinetDomainChangeSiteRequest extends FormRequest
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
            'new_site_id' => [
                'required',
                'int',
                Rule::exists('cabinet_sites', 'id'),
                new CabinetMembership(cabinet(), 'cabinet_sites'),
            ],
        ];
    }
}
