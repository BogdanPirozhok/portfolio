<?php

namespace App\Observers\Cabinet;

use App\Models\Cabinet\CabinetSite;
use App\Services\Cabinet\CabinetSiteService;

class CabinetSiteObserver
{
    /**
     * CabinetObserver constructor.
     * @param CabinetSiteService $siteService
     */
    public function __construct(protected CabinetSiteService $siteService)
    {
    }

    /**
     * Handle the Cabinet "created" event.
     */
    public function created(CabinetSite $site): void
    {
        //
    }

    /**
     * Handle the Cabinet "updating" event.
     */
    public function updating(CabinetSite $site): void
    {
        //
    }

    /**
     * Handle the Cabinet "updated" event.
     */
    public function updated(CabinetSite $site): void
    {
        $this->siteService->clearCache();
    }

    /**
     * Handle the Cabinet "deleting" event.
     */
    public function deleting(CabinetSite $site): void
    {
        //
    }

    /**
     * Handle the Cabinet "deleted" event.
     */
    public function deleted(CabinetSite $site): void
    {
        $this->siteService->clearCache();
    }

    /**
     * Handle the Cabinet "restored" event.
     */
    public function restored(CabinetSite $site): void
    {
        //
    }

    /**
     * Handle the Cabinet "force deleted" event.
     */
    public function forceDeleted(CabinetSite $site): void
    {
        //
    }
}
