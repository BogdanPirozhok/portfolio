<?php

namespace App\Http\Requests\Chat;

use App\Traits\ValidationMessages;
use Illuminate\Foundation\Http\FormRequest;

class ChatMessageStoreRequest extends FormRequest
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
            'text' => [
                'required',
                'string',
                'max:1500'
            ],
            'timestamp' => [
                'required',
                'string',
                'date'
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'text' => __('field.chat.text'),
            'timestamp' => __('field.chat.timestamp'),
        ];
    }
}
