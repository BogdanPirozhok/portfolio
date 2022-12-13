<?php

namespace App\Http\Resources\Cabinet;

use App\Models\Cabinet\CabinetSite;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CabinetSiteResource
 * @package App\Http\Resources\Cabinet
 */
class CabinetSiteResource extends JsonResource
{
    /**
     * @param  Request  $request
     */
    public function toArray($request): array
    {
        /** @var CabinetSite|self $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
            'is_deleting' => $this->isDeleting(),
            'domain' => CabinetDomainResource::make($this->whenLoaded('domain'))
        ];
    }
}
