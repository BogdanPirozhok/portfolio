<?php

use App\Models\Cabinet\Cabinet;
use App\Models\Product\Product;
use App\Models\User\User;
use App\Services\Cabinet\ACL\CabinetAccessService;
use App\Services\Cabinet\ACL\CabinetUserService;

function check_acl(int $cabinetId, int $userId, array $permissions): bool
{
    if (check_moderator($userId)) {
        return true;
    }

    /** @var CabinetAccessService $service */
    $service = resolve(CabinetAccessService::class);

    if ($service->checkPermissions($cabinetId, $userId, $permissions)) {
        return true;
    }

    return false;
}

/**
 * @param Product $product
 * @param int $userId
 * @param User $curatorUser
 * @return bool
 */
function check_curator(Product $product, int $userId, User $curatorUser): bool
{
    /** @var CabinetAccessService $cabinetAccessService */
    $cabinetAccessService = resolve(CabinetAccessService::class);

    return $cabinetAccessService->checkCurator($product, $userId, $curatorUser);
}

function check_moderator(?int $userId = null): bool
{
    /** @var CabinetAccessService $cabinetAccessService */
    $cabinetAccessService = resolve(CabinetAccessService::class);

    return $cabinetAccessService->checkModerator($userId);
}

function check_admin(?int $userId = null): bool
{
    /** @var CabinetAccessService $cabinetAccessService */
    $cabinetAccessService = resolve(CabinetAccessService::class);

    return $cabinetAccessService->checkAdmin($userId);
}

function check_owner(int $cabinetId, int $userId): bool
{
    if (check_moderator($userId)) {
        return true;
    }

    /** @var CabinetAccessService $service */
    $service = resolve(CabinetAccessService::class);

    return $service->userIsOwner($cabinetId, $userId);
}

function check_cabinet_user(int $cabinetId, int $userId): bool
{
    if (check_moderator($userId)) {
        return true;
    }

    /** @var CabinetUserService $cabinetUserService */
    $cabinetUserService = resolve(CabinetUserService::class);

    return $cabinetUserService->isCabinetUser($cabinetId, $userId);
}

function root_permission(string $permission): string
{
    return explode('.', $permission)[0];
}

function entity_permission(string $permission, int $id): string
{
    return $permission . '.' . $id;
}

function cache_role_id(?int $roleId = null): string|int
{
    if ($roleId) {
        return 'role_tree.role.' . $roleId;
    }

    /** @var Cabinet $cabinet */
    $cabinet = cabinet();

    if ($cabinet) {
        return 'role_tree.cabinet.' . $cabinet->id;
    }

    return 0;
}
