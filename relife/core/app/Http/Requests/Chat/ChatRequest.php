<?php

namespace App\Http\Requests\Chat;

use App\Traits\ValidationMessages;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChatRequest extends FormRequest
{
    use ValidationMessages;

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
            'user_id' => [
                Rule::exists('users', 'id')
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => __('field.chat.user'),
        ];
    }
}
