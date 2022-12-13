<?php

namespace App\Repositories\Cabinet;

use App\Models\Cabinet\CabinetSite;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class CabinetSiteRepositoryEloquent.
 *
 * @package namespace App\Repositories\Cabinet;
 */
class CabinetSiteRepositoryEloquent extends BaseRepository implements CabinetSiteRepository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return CabinetSite::class;
    }

    /**
     * @return Collection|CabinetSite[]
     */
    public function getDeleting(): Collection
    {
        return $this->getModel()->query()
            ->where('deleting_at', '<=', Carbon::now())
            ->get();
    }
}
