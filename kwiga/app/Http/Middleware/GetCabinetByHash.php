<?php

namespace App\Http\Middleware;

use App\Exceptions\Common\RegularException;
use App\Services\Cabinet\CabinetService;
use Closure;
use Illuminate\Http\Request;

class GetCabinetByHash
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     * @throws RegularException
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->is('health')) {
            /** @var CabinetService $cabinetService */
            $cabinetService = resolve(CabinetService::class);

            $hash = $request->header(CabinetService::CABINET_HASH);

            if (!$hash) {
                throw new RegularException(lang('error.api.cabinet_hash_request_error'));
            }

            $cabinet = $cabinetService->findByHash($hash);

            if (is_null($cabinet)) {
                throw new RegularException(lang('common.common.subdomain_not_found'));
            }

            $request->request_cabinet = $cabinet;
        }

        return $next($request);
    }
}
