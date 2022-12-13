<?php

namespace App\Http\Resources\Chat;

use App\Http\Resources\User\UserSimpleResource;
use App\Models\Chat\Chat;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Chat|self $this */

        return [
            'id' => $this->id,
            'message' => $this->when(
                $this->relationLoaded('messages'),
                ChatMessageResource::make($this->messages->first())
            ),
            'user' => $this->when(
                $this->relationLoaded('users'),
                UserSimpleResource::make($this->users->first())
            ),
        ];
    }
}
