<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Chat\ChatResource;
use App\Http\Resources\Country\CountryResource;
use App\Models\Common\Country;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property integer common_ratings_sum_rating
 *
 * @property bool is_current_user_subscribed
 */

class UserSimpleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var User|self $this */
        return [
            'id' => $this->id,
            'email' => $this->email,
            'profile' => ProfileResource::make($this->whenLoaded('profile')),
            'is_current_user_subscribed' => $this->when(
                isset($this->is_current_user_subscribed),
                $this->is_current_user_subscribed,
            ),
            'is_notify' => $this->is_notify,
            'is_complete' => $this->is_complete,
            'is_verified' => $this->isVerified(),
            'ratings_count' => $this->ratings_count,
            'partner_countries' => $this->when(
                $this->relationLoaded('partners'),
                CountryResource::collection($this->partners)
            ),
            // TODO: удалить после обновления до api/v2
            'chat' => $this->when(
                $this->relationLoaded('chats'),
                ChatResource::make($this->chats->first())
            ),
            'used_invite_code' => $this->used_invite_code,
            'is_admin' => $this->isAdmin(),
        ];
    }
}
