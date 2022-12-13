<?php

namespace App\Http\Controllers\Cabinet;

use App\Enums\ACL\PermissionEnum;
use App\Exceptions\Cabinet\CabinetACLException;
use App\Exceptions\Common\RegularException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\CabinetStoreSiteRequest;
use App\Http\Resources\Cabinet\CabinetSiteResource;
use App\Models\Cabinet\CabinetSite;
use App\Services\Cabinet\CabinetSiteService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class CabinetSiteController extends Controller
{
    public function __construct(protected CabinetSiteService $siteService)
    {
    }

    /**
     * @throws CabinetACLException
     */
    public function index(): AnonymousResourceCollection
    {
        $cabinet = cabinet();
        $this->checkCabinetACL($cabinet->id, Auth::id(), [PermissionEnum::SITE_SETTINGS_READ]);

        return CabinetSiteResource::collection($cabinet->sites);
    }

    /**
     * @throws CabinetACLException
     */
    public function show(CabinetSite $site): CabinetSiteResource
    {
        $this->checkCabinetACL($site->cabinet_id, Auth::id(), [PermissionEnum::SITE_SETTINGS_READ]);

        return CabinetSiteResource::make($site);
    }

    /**
     * @throws CabinetACLException|RegularException
     */
    public function store(CabinetStoreSiteRequest $request): CabinetSiteResource
    {
        $cabinet = cabinet();
        $this->checkCabinetACL($cabinet->id, Auth::id(), [PermissionEnum::SITE_SETTINGS_STORE]);

        $name = $request->input('name');
        $slug = $request->input('slug');

        $site = $this->siteService->addSite($name, $slug, $cabinet);

        return CabinetSiteResource::make($site);
    }

    /**
     * @throws CabinetACLException|RegularException
     */
    public function delete(CabinetSite $site): CabinetSiteResource
    {
        $this->checkCabinetACL($site->cabinet_id, Auth::id(), [PermissionEnum::SITE_SETTINGS_DELETE]);

        $site = $this->siteService->deleteRequest($site);

        return CabinetSiteResource::make($site);
    }

    /**
     * @throws CabinetACLException|RegularException
     */
    public function restore(CabinetSite $site): CabinetSiteResource
    {
        $this->checkCabinetACL($site->cabinet_id, Auth::id(), [PermissionEnum::SITE_SETTINGS_DELETE]);

        $site = $this->siteService->deleteRestore($site);

        return CabinetSiteResource::make($site);
    }

    /**
     * @throws CabinetACLException|RegularException
     */
    public function setDefault(CabinetSite $site): CabinetSiteResource
    {
        $this->checkCabinetACL($site->cabinet_id, Auth::id(), [PermissionEnum::SITE_SETTINGS_STORE]);

        $site = $this->siteService->setDefault($site);

        return CabinetSiteResource::make($site);
    }

    /**
     * @throws CabinetACLException|RegularException
     */
    public function setActive(CabinetSite $site): CabinetSiteResource
    {
        $this->checkCabinetACL($site->cabinet_id, Auth::id(), [PermissionEnum::SITE_SETTINGS_STORE]);

        $site = $this->siteService->setActive($site);

        return CabinetSiteResource::make($site);
    }
}
