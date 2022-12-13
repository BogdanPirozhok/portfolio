<?php

namespace App\Http\Middleware;

use App\Enums\EnvironmentEnum;
use App\Exceptions\Common\RegularException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HasActionsOnDomains
{
    /**
     * @throws RegularException
     */
    public function handle(Request $request, Closure $next)
    {
        if (app()->environment(EnvironmentEnum::STAGE)
            && !in_array(config('app.domain'), config('cloudflare.available_stages', []))
        ) {
            throw new RegularException('Service is not available at this stage', Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
