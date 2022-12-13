<?php

namespace App\Repositories\Cabinet;

use App\Models\Cabinet\CabinetDomain;
use Illuminate\Support\Collection;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface CabinetDomainRepository.
 *
 * @package namespace App\Repositories\Cabinet;
 */
interface CabinetDomainRepository extends RepositoryInterface
{
    /**
     * @return Collection|CabinetDomain[]
     */
    public function getInactive(): Collection;

    public function findByHost(string $hostname): ?CabinetDomain;
}
