<?php

namespace App\Console\Commands\Cabinet;

use App\Services\Cabinet\CabinetDomainCheckService;
use Illuminate\Console\Command;
use Throwable;

class CabinetActivateDomain extends Command
{
    protected $signature = 'cabinet:activate-domain';

    protected $description = 'Check DNS records';

    public function __construct(protected CabinetDomainCheckService $domainCheckService)
    {
        parent::__construct();
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $this->domainCheckService->checkDNS();
    }
}
