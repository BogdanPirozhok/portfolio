<?php

namespace App\Http\Requests\Chat;

use App\Traits\ValidationMessages;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChatReadMessagesRequest extends FormRequest
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
            'message_ids' => [
                'array',
                'required',
            ],
            'message_ids.*' => [
                Rule::exists('chat_messages', 'id')
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'message_ids' => __('field.chat.messages'),
        ];
    }
}
