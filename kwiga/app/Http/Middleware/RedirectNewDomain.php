<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectNewDomain
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $host = parse_url($request->url(), PHP_URL_HOST);

        if (preg_match('/.*ojowo.com$/', (string) $host)) {
            $newHost = preg_replace('/(.*)(ojowo.com)$/', '$1kwiga.com', $host);
            $newUrl = str_replace($host, $newHost, $request->url());
            $newUrl = str_replace('http://', 'https://', $newUrl);

            return redirect()->to($newUrl)->setStatusCode(Response::HTTP_MOVED_PERMANENTLY);
        }

        return $next($request);
    }
}
