<?php

namespace App\Http\Views;

use App\Models\User\User;
use Exception;
use Illuminate\View\View;

class TimeoutScriptsComposer
{

    /**
     * ProfileComposer constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * Bind data to the view.
     *
     * @throws Exception
     */
    public function compose(View $view): void
    {
        $liveChatEnabled = config('livechat.enabled');
        $liveChatLicense = config('livechat.license');

        if ($liveChatEnabled) {
            if (is_null(request()->route())) {
                $liveChatEnabled = false;
            } else {
                /** @var User $user */
                $user = auth()->user();

                if (cabinet()
                    && !request()->routeIs(
                        'expert',
                        'locale.expert',
                        'expert.any',
                        'locale.expert.any',
                        'cabinet.create-account.any',
                        'locale.cabinet.create-account.any',
                    ) || request()->routeIs(
                        'profile',
                        'locale.profile'
                    ) || !is_null($user) && ($user->isAdmin() || $user->isModerator() || $user->isContentUser())
                ) {
                    $liveChatEnabled = false;
                }
            }
        }

        $view->with(
            compact([
                'liveChatEnabled',
                'liveChatLicense',
            ])
        );
    }
}
