<?php

namespace App\Http\Middleware;

use App\Models\Cabinet\CabinetSite;
use Closure;
use Illuminate\Http\Request;

class OnlyActiveDomain
{
    public function handle(Request $request, Closure $next)
    {
        /** @var CabinetSite $site */
        $site = cabinetSite();

        if ($site && !$site->activeDomain) {
            $route = currentLocaleRouteWithForcedRoot($request->route()->getName() ?? 'home');

            return redirect($route);
        }

        return $next($request);
    }
}
