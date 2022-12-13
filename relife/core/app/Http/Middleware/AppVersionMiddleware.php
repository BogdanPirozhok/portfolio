<?php

namespace App\Http\Middleware;

use App\Models\User\User;
use Closure;

class AppVersionMiddleware extends \Exception
{
    public function handle($request, Closure $next)
    {
        $version = $request->header('App-Version');

        if ($version) {
            /** @var User $user */
            $user = auth()->user();

            if ($user->current_version_app !== $version) {
                $user->current_version_app = $version;
                $user->save();
            }
        }

        return $next($request);
    }
}
