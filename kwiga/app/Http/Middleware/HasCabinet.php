<?php

namespace App\Http\Middleware;

use App\Models\Cabinet\Cabinet;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HasCabinet
{
    public function handle(Request $request, Closure $next)
    {
        if (!isset($request->request_cabinet) || !$request->request_cabinet instanceof Cabinet) {
            throw new NotFoundHttpException();
        }

        return $next($request);
    }
}
