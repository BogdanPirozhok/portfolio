<?php

use App\Models\Cabinet\Cabinet;
use App\Models\Cabinet\CabinetSite;
use App\Services\Cabinet\CabinetService;

/**
 * Get cabinet from request
 */
function cabinet(): ?Cabinet
{
    return request()->request_cabinet ?? null;
}

function setCabinet(?Cabinet $cabinet): ?Cabinet
{
    request()->request_cabinet = $cabinet;

    return cabinet();
}

function cabinetSite(): ?CabinetSite
{
    return request()->request_site ?? null;
}

function getLastActiveCabinet(): ?Cabinet
{
    /** @var CabinetService $cabinetService */
    $cabinetService = resolve(CabinetService::class);
    return $cabinetService->getLastActiveCabinet();
}

function cabinetById(int $id): ?Cabinet
{
    /** @var CabinetService $cabinetService */
    $cabinetService = resolve(CabinetService::class);
    return $cabinetService->find($id);
}
