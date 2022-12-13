<?php

namespace App\Repositories\Cabinet;

use App\Models\Cabinet\CabinetSite;
use Illuminate\Support\Collection;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface CabinetSiteRepository.
 *
 * @package namespace App\Repositories\Cabinet;
 */
interface CabinetSiteRepository extends RepositoryInterface
{
    /**
     * @return Collection|CabinetSite[]
     */
    public function getDeleting(): Collection;
}
