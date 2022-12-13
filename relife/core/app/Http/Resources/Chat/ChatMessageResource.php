<?php

namespace App\Http\Resources\Chat;

use App\Http\Resources\User\UserSimpleResource;
use App\Models\Chat\ChatMessage;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessageResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var ChatMessage|self $this */

        return [
            'id' => $this->id,
            'text' => $this->text,
            'is_read' => $this->when(
                $this->relationLoaded('reads'),
                $this->isRead()
            ),
            'user' => UserSimpleResource::make($this->whenLoaded('user')),
            'chat_id' => $this->chat_id,
            'sent_at' => $this->sent_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
