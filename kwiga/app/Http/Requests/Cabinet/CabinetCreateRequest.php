<?php

namespace App\Http\Requests\Cabinet;

use App\Models\Cabinet\ACL\CabinetRole;
use App\Models\Cabinet\CabinetType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class CabinetCreateRequest
 * @package App\Http\Requests\User
 *
 * @property string name
 * @property int type_id
 */
class CabinetCreateRequest extends FormRequest
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
                'max:255',
                Rule::unique('cabinet_sites', 'name')
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
            'type_id' => [
                'nullable',
                'in:' . implode(',', CabinetType::EXPERT_TYPES)
            ],
            'cabinet_id' => [
                Rule::requiredIf($this->get('roles')),
                'nullable',
                'integer',
            ],
            'roles' => [
                'nullable',
                'array'
            ],
            'roles.*' => [
                'int',
                Rule::exists('cabinet_roles', 'id')
                    ->where('cabinet_id', $this->get('cabinet_id'))
                    ->where('type_id', CabinetRole::TYPE_CUSTOM)
                    ->whereNull('deleted_at')
            ]
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
