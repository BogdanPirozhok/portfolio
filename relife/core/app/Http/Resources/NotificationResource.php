<?php

namespace App\Http\Resources;

use App\Http\Resources\User\UserSimpleResource;
use App\Models\Common\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Notification|self $this */

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'notice_title' => $this->notice_title,
            'notice_text' => $this->notice_text,
            'notice_links' => $this->notice_links,
            'user' => UserSimpleResource::make($this->whenLoaded('user'))
        ];
    }
}
