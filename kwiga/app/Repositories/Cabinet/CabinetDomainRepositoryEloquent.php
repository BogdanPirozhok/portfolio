<?php

namespace App\Repositories\Cabinet;

use App\Models\Cabinet\CabinetDomain;
use Prettus\Repository\Eloquent\BaseRepository;
use Illuminate\Support\Collection;

/**
 * Class CabinetDomainRepositoryEloquent.
 *
 * @package namespace App\Repositories\Cabinet;
 */
class CabinetDomainRepositoryEloquent extends BaseRepository implements CabinetDomainRepository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return CabinetDomain::class;
    }

    /**
     * @return Collection<int, CabinetDomain>
     */
    public function getInactive(): Collection
    {
        return $this->getModel()->query()
            ->whereNotNull('confirmed_at')
            ->get();
    }

    public function findByHost(string $hostname): ?CabinetDomain
    {
        /** @var CabinetDomain $cabinetDomain */
        $cabinetDomain = $this->getModel()->query()
            ->withTrashed()
            ->where('hostname', '=', $hostname)
            ->orderByDesc('updated_at')
            ->first();

        return $cabinetDomain;
    }
}
