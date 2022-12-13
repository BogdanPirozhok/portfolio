<?php

namespace App\Console\Commands\Cabinet;

use App\Services\Cabinet\CabinetSiteService;
use Illuminate\Console\Command;
use Throwable;

class CabinetDeleteSite extends Command
{
    protected $signature = 'cabinet:site-delete';

    protected $description = 'Delete cabinet site';

    public function __construct(protected CabinetSiteService $siteService)
    {
        parent::__construct();
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $this->siteService->removalTask();
    }
}
