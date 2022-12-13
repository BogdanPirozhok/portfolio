<?php

namespace App\Services\Cabinet;

use App\Models\Cabinet\CabinetDomain;
use App\Repositories\Cabinet\CabinetDomainRepositoryEloquent;
use Illuminate\Support\Carbon;
use JetBrains\PhpStorm\ArrayShape;
use Throwable;

class CabinetDomainCheckService
{
    protected CabinetDomainRepositoryEloquent $domainRepository;
    protected DomainApi $domainApi;

    public function __construct()
    {
        $this->domainRepository = resolve(CabinetDomainRepositoryEloquent::class);
        $this->domainApi = resolve(DomainApi::class);
    }

    /**
     * @throws Throwable
     */
    public function checkDNS(): void
    {
        $domains = $this->domainRepository->getInactive();
        $now = Carbon::now();

        foreach ($domains as $domain) {
            $checkDelayMinutes = 1;

            if ($domain->isActive()) {
                // if the domain is already active, we check only once every 6 hours
                $checkDelayMinutes = 6 * 60;
            } elseif ($domain->isFailed()) {
                // if errors are found in the domain, we perform a check only once every 15 minutes
                $checkDelayMinutes = 15;
            }

            if ($now->diffInMinutes($domain->updated_at) >= $checkDelayMinutes) {
                if ($domain->isRootDomain()) {
                    $result = $this->checkRootDomainDNS($now, $domain);
                } else {
                    $result = $this->checkSubDomainDNS($domain);
                }

                if ($result['done']) {
                    $domain->is_active = true;
                    $domain->dns_errors = null;
                } else {
                    if ($domain->is_active) {
                        $domain->dns_errors = $result['errors'];
                    }
                    $domain->is_active = false;
                }

                $domain->updated_at = $now;
                $domain->save();
            }
        }
    }

    /**
     * @throws Throwable
     */
    #[ArrayShape([
        'done' => 'bool',
        'errors' => 'array|null'
    ])]
    private function checkRootDomainDNS(Carbon $now, CabinetDomain $domain): array
    {
        $DNSRecords = collect(dns_get_record($domain->hostname, DNS_NS));
        $checkNS = $DNSRecords->pluck('target');

        if (!$checkNS->diff($domain->dns_records)->count()) {
            $hostData = $this->domainApi->checkRecords($domain->cloudflare_id);

            if ($hostData['status'] !== 'active') {
                $checkDelayMinutes = $domain->last_activation_attempt_at ? 61 : 5;

                if ($now->diffInMinutes($domain->last_activation_attempt_at) < $checkDelayMinutes) {
                    return [
                        'done' => false,
                        'errors' => null,
                    ];
                }

                $this->domainApi->activateRootDomain($domain->cloudflare_id);

                $domain->last_activation_attempt_at = $now;
                $domain->save();

                // Don't make requests to the Cloudflare API too often
                sleep(5);

                $hostData = $this->domainApi->checkRecords($domain->cloudflare_id);
            }

            if ($hostData['status'] === 'active') {
                $this->domainApi->setRecord($domain->cloudflare_id, [
                    'type' => 'CNAME',
                    'name' => '@',
                    'content' => config('cloudflare.cname_record')
                ]);

                $this->domainApi->setRecord($domain->cloudflare_id, [
                    'type' => 'CNAME',
                    'name' => 'www',
                    'content' => config('cloudflare.cname_record')
                ]);

                $this->domainApi->setUseHTTPS($domain->cloudflare_id);
            } else {
                $errors = $hostData['errors'] ?? [];

                if (!count($errors)) {
                    $errors = [lang('cabinet.domain.ns_not_found')];
                }
            }
        } else {
            $errors = [lang('cabinet.domain.ns_not_found')];
        }

        return [
            'done' => !isset($errors),
            'errors' => $errors ?? null,
        ];
    }

    /**
     * @throws Throwable
     */
    #[ArrayShape([
        'done' => 'bool',
        'errors' => 'array|null'
    ])]
    private function checkSubDomainDNS(CabinetDomain $domain): array
    {
        $DNSRecords = collect(dns_get_record($domain->hostname, DNS_CNAME));
        $checkCNAME = $DNSRecords->firstWhere('target', '=', config('cloudflare.ssl_host_cname'));

        if ($checkCNAME && $DNSRecords->count() === 1) {
            if (!$domain->cloudflare_id) {
                $newHost = $this->domainApi->addSubDomain($domain->hostname);

                $domain->cloudflare_id = $newHost['id'];
                $domain->save();

                // Don't make requests to the Cloudflare API too often
                sleep(5);
            }
            $hostData = $this->domainApi->checkSubDomain($domain->cloudflare_id);

            if ($hostData['ssl']['status'] !== 'active' || $hostData['status'] !== 'active') {
                $validationErrors = $hostData['ssl']['validation_errors'] ?? [];
                $hostErrors = $hostData['errors'] ?? [];

                $errors = [...$validationErrors, ...$hostErrors];

                if (!count($errors)) {
                    $errors = [lang('cabinet.domain.unknown_dns_errors')];
                }
            }
        } else {
            $errors = [lang('cabinet.domain.cname_not_found')];
        }

        return [
            'done' => !isset($errors),
            'errors' => $errors ?? null,
        ];
    }
}
