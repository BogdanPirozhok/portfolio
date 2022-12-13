<?php

namespace App\Http\Controllers\Cabinet;

use App\Enums\ACL\PermissionEnum;
use App\Exceptions\Cabinet\CabinetACLException;
use App\Exceptions\Common\RegularException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\CabinetDomainChangeSiteRequest;
use App\Http\Requests\Cabinet\CabinetStoreDomainRequest;
use App\Http\Resources\Cabinet\CabinetDomainResource;
use App\Models\Cabinet\CabinetDomain;
use App\Models\Cabinet\CabinetSite;
use App\Services\Cabinet\CabinetDomainService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class CabinetDomainController extends Controller
{
    protected CabinetDomainService $domainService;

    public function __construct()
    {
        $this->domainService = resolve(CabinetDomainService::class);
    }

    /**
     * @throws CabinetACLException|RegularException|Throwable
     */
    public function store(CabinetStoreDomainRequest $request, CabinetSite $site): CabinetDomainResource
    {
        $this->checkCabinetACL($site->cabinet_id, Auth::id(), [PermissionEnum::SITE_SETTINGS_STORE]);

        $hostname = $request->input('hostname');
        $typeId = $request->input('type_id');

        $domain = $this->domainService->addDomain($hostname, $typeId, $site);

        return CabinetDomainResource::make($domain);
    }

    /**
     * @throws CabinetACLException|RegularException
     */
    public function destroy(CabinetDomain $domain): JsonResponse
    {
        $this->checkCabinetACL($domain->site->cabinet_id, Auth::id(), [PermissionEnum::SITE_SETTINGS_DELETE]);

        $this->domainService->destroyDomain($domain);

        return response()->json(['success' => true], ResponseAlias::HTTP_NO_CONTENT);
    }

    /**
     * @throws CabinetACLException
     */
    public function setConfirm(CabinetDomain $domain): CabinetDomainResource
    {
        $this->checkCabinetACL($domain->site->cabinet_id, Auth::id(), [PermissionEnum::SITE_SETTINGS_STORE]);

        return CabinetDomainResource::make(
            $this->domainService->setConfirmed($domain)
        );
    }

    /**
     * @throws CabinetACLException|RegularException
     */
    public function changeSite(CabinetDomainChangeSiteRequest $request, CabinetDomain $domain): CabinetDomainResource
    {
        $this->checkCabinetACL($domain->site->cabinet_id, Auth::id(), [PermissionEnum::SITE_SETTINGS_STORE]);

        return CabinetDomainResource::make(
            $this->domainService->changeSite($domain, $request->input('new_site_id'))
        );
    }
}
