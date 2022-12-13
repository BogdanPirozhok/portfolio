<?php

namespace App\Observers\Cabinet;

use App\Models\Cabinet\CabinetDomain;
use App\Services\Cabinet\CabinetDomainService;

class CabinetDomainObserver
{
    /**
     * CabinetObserver constructor.
     * @param CabinetDomainService $domainService
     */
    public function __construct(protected CabinetDomainService $domainService)
    {
    }

    /**
     * Handle the Cabinet "created" event.
     */
    public function created(CabinetDomain $domain): void
    {
        $this->domainService->reloadRequestData($domain);
    }

    /**
     * Handle the Cabinet "updating" event.
     */
    public function updating(CabinetDomain $domain): void
    {
        //
    }

    /**
     * Handle the Cabinet "updated" event.
     */
    public function updated(CabinetDomain $domain): void
    {
        $this->domainService->reloadRequestData($domain);
        $this->domainService->clearCacheByHost($domain->hostname);
    }

    /**
     * Handle the Cabinet "deleting" event.
     */
    public function deleting(CabinetDomain $domain): void
    {
        $domain->is_active = false;
        $domain->save();
    }

    /**
     * Handle the Cabinet "deleted" event.
     */
    public function deleted(CabinetDomain $domain): void
    {
        $this->domainService->reloadRequestData($domain);
        $this->domainService->clearCacheByHost($domain->hostname);
    }

    /**
     * Handle the Cabinet "restored" event.
     */
    public function restored(CabinetDomain $domain): void
    {
        //
    }

    /**
     * Handle the Cabinet "force deleted" event.
     */
    public function forceDeleted(CabinetDomain $domain): void
    {
        //
    }
}
