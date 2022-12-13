<?php

use App\Contracts\Available;
use App\Exceptions\AvailableException;
use App\Models\User\User;
use Illuminate\Support\Facades\Auth;

/**
 * @throws AvailableException
 */
function check_available_action(Available $model, ?User $user = null): void
{
    if (is_null($user)) {
        /** @var User $user */
        $user = Auth::user();
    }

    $checkDisabled = $user->disablingUsers
        ->whereIn('id', $model->getOwnerIds())
        ->first();

    if ($checkDisabled) {
        throw new AvailableException($model->getAvailableMessage());
    }
}

/**
 * @throws AvailableException
 */
function check_available_user(int $userId, ?User $user = null): void
{
    if (is_null($user)) {
        /** @var User $user */
        $user = Auth::user();
    }

    $checkDisabled = $user->disablingUsers
        ->where('id', '=', $userId)
        ->first();

    if ($checkDisabled) {
        throw new AvailableException(__('errors.not_available_in_user'));
    }
}
