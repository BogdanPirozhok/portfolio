<?php

namespace App\Http\Resources\Cabinet;

use App\Models\Cabinet\CabinetDomain;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CabinetDomainResource
 * @package App\Http\Resources\Cabinet
 */
class CabinetDomainResource extends JsonResource
{
    /**
     * @param  Request  $request
     */
    public function toArray($request): array
    {
        /** @var CabinetDomain|self $this */
        $dnsRecords = [];

        if ($this->isRootDomain() && !is_null($this->dns_records)) {
            foreach ($this->dns_records as $dns_record) {
                $dnsRecords[] = [
                    'type' => 'NS',
                    'value' => $dns_record
                ];
            }
        } else {
            $dnsRecords[] = [
                'type' => 'CNAME',
                'name' => $this->hostname,
                'value' => config('cloudflare.ssl_host_cname')
            ];
        }

        return [
            'id' => $this->id,
            'hostname' => $this->hostname,
            'is_root_domain' => $this->isRootDomain(),
            'is_confirmed' => $this->isConfirmed(),
            'is_active' => $this->isActive(),
            'is_failed' => $this->isFailed(),
            'cloudflare_id' => $this->cloudflare_id,
            'dns_records' => $dnsRecords,
            'dns_errors' => $this->dns_errors
        ];
    }
}
