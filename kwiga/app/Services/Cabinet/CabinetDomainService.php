<?php

namespace App\Services\Cabinet;

use App\Enums\EnvironmentEnum;
use App\Exceptions\Common\RegularException;
use App\Models\Cabinet\CabinetDomain;
use App\Models\Cabinet\CabinetSite;
use App\Repositories\Cabinet\CabinetDomainRepository;
use App\Services\System\RouteService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class CabinetDomainService
{
    public function __construct(
        protected CabinetSiteService $siteService,
        protected CabinetDomainRepository $domainRepository,
        protected DomainApi $domainApi
    ) {
    }

    const CACHE_KEY_BY_HOST = 'cabinet_domain_by_host.';

    public function findDomainByHost(string $hostname): ?CabinetDomain
    {
        $cacheKey = self::CACHE_KEY_BY_HOST . $hostname;

        if (!($domain = Cache::get($cacheKey))) {
            $domain = $this->domainRepository->findByHost($hostname);

            if (!is_null($domain)) {
                Cache::put($cacheKey, $domain, now()->addDay());
            }
        }

        return $domain;
    }

    public function clearCacheByHost($hostname): void
    {
        Cache::forget(self::CACHE_KEY_BY_HOST . $hostname);
    }

    public function findDomainById(int $domainId): ?CabinetDomain
    {
        return $this->domainRepository->find($domainId);
    }

    /**
     * @throws RegularException
     */
    public function addDomain(string $hostname, string $typeId, CabinetSite $site): CabinetDomain
    {
        if ($site->domain) {
            throw new RegularException(lang('cabinet.domain.only_one'));
        }

        if (!app()->environment([EnvironmentEnum::LOCAL])) {
            $checkHostName = $hostname;

            if ((int) $typeId === CabinetDomain::SUB_DOMAIN_TYPE) {
                $hostNameParts = explode('.', $checkHostName);
                array_shift($hostNameParts);

                $checkHostName = implode('.', $hostNameParts);
            }
            $checkDNS = dns_get_record($checkHostName);

            if (!count($checkDNS)) {
                throw new RegularException(lang('cabinet.domain.host_not_found'));
            }
        }

        try {
            if (!app()->environment([EnvironmentEnum::LOCAL])) {
                if ((int) $typeId === CabinetDomain::ROOT_DOMAIN_TYPE) {
                    $hostData = $this->domainApi->addRootDomain($hostname);
                    $dnsRecords = $hostData['name_servers'];
                }
            }

            /** @var CabinetDomain $domain */
            $domain = $site->domain()->create([
                'hostname' => $hostname,
                'type_id' => $typeId,
                'cloudflare_id' => $hostData['id'] ?? null,
                'dns_records' => $dnsRecords ?? null
            ]);

            if (app()->environment([EnvironmentEnum::LOCAL])) {
                $domain->is_active = true;
                $domain->dns_errors = null;
                $domain->save();
            }

            return $domain;
        } catch (Throwable $e) {
            Log::info($e);
            throw new RegularException($e->getMessage());
        }
    }

    /**
     * @throws RegularException
     */
    public function destroyDomain(CabinetDomain $domain): void
    {
        try {
            if ($domain->cloudflare_id) {
                $this->deleteByApi($domain);
            }

            $domain->delete();
        } catch (Throwable $e) {
            Log::info($e);
            throw new RegularException($e->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function deleteByApi(CabinetDomain $domain): void
    {
        if ($domain->isRootDomain()) {
            $this->domainApi->deleteRootDomain($domain->cloudflare_id);
        } else {
            $this->domainApi->deleteSubDomain($domain->cloudflare_id);
        }
    }

    public function setConfirmed(CabinetDomain $domain): CabinetDomain
    {
        $domain->confirmed_at = Carbon::now();
        $domain->save();

        return $this->findDomainById($domain->id);
    }

    /**
     * @throws RegularException
     */
    public function changeSite(CabinetDomain $domain, int $newSiteId): CabinetDomain
    {
        $newSite = $this->siteService->findById($newSiteId);

        if ($newSite->domain) {
            throw new RegularException(lang('cabinet.domain.domain_already_exists'));
        }
        if ($newSite->cabinet_id !== $domain->site->cabinet_id) {
            throw new RegularException(lang('cabinet.domain.no_access_to_site'), ResponseAlias::HTTP_FORBIDDEN);
        }

        $domain->cabinet_site_id = $newSiteId;
        $domain->save();

        return $this->findDomainById($domain->id);
    }

    public function reloadRequestData(CabinetDomain $domain): void
    {
        /** @var RouteService $routeService */
        $routeService = resolve(RouteService::class);
        $routeService->clearCachedRoutes($domain->site->cabinet_id);
    }
}
