<?php

namespace App\Services\Cabinet\ACL;

use App\Enums\ACL\PermissionEnum;
use App\Exceptions\Cabinet\CabinetACLException;
use App\Models\Account\Account;
use App\Models\Cabinet\Cabinet;
use App\Models\Product\Product;
use App\Models\User\User;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

trait CabinetAccessTrait
{

    /**
     * @throws CabinetACLException
     */
    public function checkCabinetACL(?int $cabinetId, ?int $userId, array $permissionsSlugs): bool
    {
        if (!is_null($userId)) {
            if (!is_null($cabinetId) && check_acl($cabinetId, $userId, $permissionsSlugs)) {
                return true;
            } elseif (check_moderator($userId)) {
                return true;
            }
        }

        throw new CabinetACLException(
            $this->getPermissionsException($permissionsSlugs)
        );
    }

    /**
     * @param Product $product
     * @param int $userId
     * @param User $curatorUser
     * @return bool
     * @throws CabinetACLException
     */
    public function checkCuratorACL(Product $product, int $userId, User $curatorUser): bool
    {
        if (check_curator($product, $userId, $curatorUser)) {
            return true;
        }

        throw new CabinetACLException(
            $this->getPermissionsException([
                PermissionEnum::COURSE_CURATOR
            ])
        );
    }

    /**
     * @param Account $account
     * @param int|null $userId
     * @param array $permissionsSlugs
     * @return bool
     * @throws CabinetACLException
     */
    public function checkAccountCabinetsACL(Account $account, ?int $userId, array $permissionsSlugs): bool
    {
        if ($userId) {
            $checkCabinets = $account->cabinets->filter(function (Cabinet $cabinet) use ($userId, $permissionsSlugs) {
                return check_acl($cabinet->id, $userId, $permissionsSlugs);
            });

            if ($checkCabinets->count()) {
                return true;
            }
        }

        throw new CabinetACLException(
            $this->getPermissionsException($permissionsSlugs)
        );
    }

    /**
     * @param array $permissionsSlugs
     * @return string
     */
    private function getPermissionsException(array $permissionsSlugs): string
    {
        /** @var CabinetPermissionService $permissionService */
        $permissionService = resolve(CabinetPermissionService::class);
        $permissions = $permissionService->collectPermissionsNames($permissionsSlugs);

        return $permissions->implode(', ');
    }

    /**
     * @throws CabinetACLException
     */
    public function checkOwner(null|int|Cabinet $cabinet = null, null|int|User $user = null): bool
    {
        $cabinetId = $this->getCabinetIdByParam($cabinet);
        $userId = $this->getUserIdByParam($user);

        if ($cabinetId && $userId && check_owner($cabinetId, $userId)) {
            return true;
        }

        throw new CabinetACLException();
    }

    /**
     * @throws CabinetACLException
     */
    public function checkCabinetUser(null|int|Cabinet $cabinet = null, null|int|User $user = null): bool
    {
        $cabinetId = $this->getCabinetIdByParam($cabinet);
        $userId = $this->getUserIdByParam($user);

        if ($cabinetId && $userId && check_cabinet_user($cabinetId, $userId)) {
            return true;
        }

        throw new CabinetACLException();
    }

    public function checkModerator(): bool
    {
        if (!check_moderator()) {
            $this->abortForbidden();
        }

        return true;
    }

    protected function getCabinetIdByParam(null|int|Cabinet $cabinet = null): ?int
    {
        if ($cabinet instanceof Cabinet) {
            $cabinetId = $cabinet->id;
        } elseif (empty($cabinet)) {
            $cabinetId = optional(cabinet())->id;
        } else {
            $cabinetId = $cabinet;
        }

        return $cabinetId;
    }

    protected function getUserIdByParam(null|int|User $user = null): ?int
    {
        if ($user instanceof User) {
            $userId = $user->id;
        } elseif (empty($user)) {
            $userId = auth()->id();
        } else {
            $userId = $user;
        }

        return $userId;
    }

    protected function abortForbidden(): never
    {
        abort(ResponseAlias::HTTP_FORBIDDEN, lang('error.exception.access_denied'));
    }

    protected function abortNotFound(): never
    {
        abort(ResponseAlias::HTTP_NOT_FOUND);
    }
}
