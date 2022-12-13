<?php

namespace App\Http\Middleware;

use App\Services\Cabinet\CabinetService;
use Closure;
use Exception;
use Illuminate\Http\Request;

class ActiveCabinet
{

    /**
     * ActiveCabinet constructor.
     * @param CabinetService $cabinetService
     */
    public function __construct(private readonly CabinetService $cabinetService)
    {
    }

    /**
     * Set last active cabinet, after all logic
     *
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $cabinetId = optional(cabinet())->id;
        $this->cabinetService->setLastActiveCabinet($cabinetId);

        return $response;
    }
}
