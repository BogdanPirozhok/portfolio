<?php

namespace App\Services\Cabinet;

use App\Exceptions\Common\RegularException;
use App\Models\Cabinet\Cabinet;
use App\Models\Cabinet\CabinetSite;
use App\Repositories\Cabinet\CabinetRepository;
use App\Repositories\Cabinet\CabinetSiteRepository;
use App\Services\System\RouteService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Prettus\Validator\Exceptions\ValidatorException;
use Throwable;

class CabinetSiteService
{
    private array $foundedSites = [];

    public function __construct(
        protected CabinetSiteRepository $siteRepository,
        protected CabinetRepository $cabinetRepository
    ) {
    }

    const CACHE_KEY_BY_ID = 'cabinet_site_by_id';
    const CACHE_KEY_BY_SLUG = 'cabinet_site_by_slug';
    const CACHE_TAG = 'cabinet_site';

    public function findById(int $siteId): ?CabinetSite
    {
        $cacheKey = self::CACHE_KEY_BY_ID . $siteId;

        if (!($site = Cache::tags(self::CACHE_TAG)->get($cacheKey))) {
            $site = $this->siteRepository->find($siteId);

            if (!is_null($site)) {
                Cache::tags(self::CACHE_TAG)->put($cacheKey, $site, now()->addDay());
            }
        }

        return $site;
    }

    public function findBySlug(string $slug): ?CabinetSite
    {
        $cacheKey = self::CACHE_KEY_BY_SLUG . $slug;

        if (!($site = Cache::tags(self::CACHE_TAG)->get($cacheKey))) {
            $site = $this->siteRepository->findByField('slug', $slug)->first();

            if (!is_null($site)) {
                Cache::tags(self::CACHE_TAG)->put($cacheKey, $site, now()->addDay());
            }
        }

        return $site;
    }

    public function clearCache(): void
    {
        Cache::tags(self::CACHE_TAG)->flush();
    }

    /**
     * @throws RegularException
     */
    public function addSite(string $name, string $slug, Cabinet $cabinet): CabinetSite
    {
        try {
            if ($cabinet->isAvailableSitesLimit()) {
                /** @var CabinetSite $site */
                $site = $cabinet->sites()->create([
                    'name' => $name,
                    'slug' => $slug
                ]);

                /** @var RouteService $routeService */
                $routeService = resolve(RouteService::class);
                $routeService->clearCachedRoutes($cabinet->id);

                return $site;
            }
            throw new RegularException(lang('cabinet.domain.exceeded_limit'));
        } catch (Throwable $e) {
            Log::info($e);
            throw new RegularException($e->getMessage());
        }
    }

    /**
     * @throws RegularException
     */
    public function setDefault(CabinetSite $site): CabinetSite
    {
        try {
            DB::beginTransaction();

            $site->cabinet->sites()->update([
                'is_default' => false,
            ]);

            if ($site->deleting_at) {
                throw new RegularException(lang('cabinet.domain.default_deleted_domain'));
            }

            $site->is_default = true;
            $site->is_active = true;
            $site->save();

            DB::commit();

            /** @var RouteService $routeService */
            $routeService = resolve(RouteService::class);
            $routeService->clearCachedRoutes($site->cabinet->id);

            return $site;
        } catch (Throwable $e) {
            Log::info($e);
            DB::rollBack();

            throw new RegularException($e->getMessage());
        }
    }

    /**
     * @throws RegularException
     */
    public function setActive(CabinetSite $site): CabinetSite
    {
        try {
            if ($site->is_active && $site->is_default) {
                throw new RegularException(lang('cabinet.domain.cannot_be_disabled'));
            }

            $site->is_active = !$site->is_active;
            $site->save();

            return $site;
        } catch (Throwable $e) {
            Log::info($e);
            throw new RegularException($e->getMessage());
        }
    }

    /**
     * @throws RegularException
     */
    public function deleteRequest(CabinetSite $site): CabinetSite
    {
        try {
            if ($site->is_default) {
                throw new RegularException(lang('cabinet.domain.cannot_be_deleted'));
            }

            $site->deleting_at = Carbon::now()->addHours(24);
            $site->save();

            return $site;
        } catch (Throwable $e) {
            Log::info($e);
            throw new RegularException($e->getMessage());
        }
    }

    /**
     * @throws RegularException
     */
    public function deleteRestore(CabinetSite $site): CabinetSite
    {
        try {
            if ($site->deleting_at > Carbon::now()) {
                $site->deleting_at = null;
                $site->save();

                return $site;
            } else {
                throw new RegularException(lang('cabinet.domain.delete_request.cannot_be_undone'));
            }
        } catch (Throwable $e) {
            Log::info($e);
            throw new RegularException($e->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function removalTask(): void
    {
        $sites = $this->siteRepository->getDeleting();

        try {
            DB::beginTransaction();

            /** @var CabinetDomainService $domainService */
            $domainService = resolve(CabinetDomainService::class);

            foreach ($sites as $site) {
                if ($site->domain->cloudflare_id) {
                    $domainService->deleteByApi($site->domain);
                }

                $site->delete();
            }

            DB::commit();
        } catch (Throwable $e) {
            Log::info($e);
            DB::rollBack();

            throw new RegularException($e->getMessage());
        }
    }

    /**
     * @throws RegularException
     */
    public function dataStructureUpdate(): void
    {
        $cabinets = $this->cabinetRepository->all();

        /** @var Cabinet $cabinet */
        foreach ($cabinets as $cabinet) {
            if (isset($cabinet->name) && isset($cabinet->slug)) {
                $site = $this->addSite($cabinet->name, $cabinet->slug, $cabinet);
                $this->setDefault($site);
            }
            if (!$cabinet->getDefaultSite()) {
                $name = 'untitled-' . $cabinet->id;

                $site = $this->addSite($name, $name, $cabinet);
                $this->setDefault($site);
            }
        }
    }

    /**
     * @throws ValidatorException
     */
    public function update(CabinetSite $site, array $data): void
    {
        $this->siteRepository->update($data, $site->id);
    }

    public function getDefaultSiteInCabinet(Cabinet $cabinet): CabinetSite
    {
        if (!array_key_exists($cabinet->id, $this->foundedSites)) {
            $this->foundedSites[$cabinet->id] = $cabinet->getDefaultSite();
        }

        return $this->foundedSites[$cabinet->id];
    }
}
