<?php

namespace App\Http\Middleware;

use Closure;

class LanguageMiddleware extends \Exception
{
    public function handle($request, Closure $next)
    {
        $locale = $request->header('Locale') ?? 'en';
        app()->setlocale($locale);
        return $next($request);
    }
}
