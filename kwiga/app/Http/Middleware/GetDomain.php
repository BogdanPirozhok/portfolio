<?php

namespace App\Http\Middleware;

use App\Services\Cabinet\CabinetDomainService;
use App\Services\Cabinet\CabinetSiteService;
use Closure;
use Illuminate\Http\Request;

class GetDomain
{
    protected array $exceptedSubdomains = [
        'api',
        'ojowo',
        'kwiga',
        'ojowo-new',
        'stage1',
        'stage2',
        'stage3',
        'stage4',
        'stage5',
        'liveeks',
        'app',
        'localhost',
        'ignite',
        'unlock',
        'sns'
    ];

    private function getExceptedSubdomains(): array
    {
        $items = $this->exceptedSubdomains;

        foreach ($items as &$item) {
            $item .= '.'. config('app.domain');
        }

        return $items;
    }

    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $rootDomain = config('app.domain');

        if (in_array($host, config('app.404_domains', []))) {
            abort(404);
        }

        if (!$request->is('health')
            && $host !== $rootDomain
            && !in_array($host, $this->getExceptedSubdomains())
        ) {
            $hostArray = explode($rootDomain, $host);
            $isSubdomain = !array_pop($hostArray);

            if (!$isSubdomain) {
                /** @var CabinetDomainService $cabinetDomainService */
                $cabinetDomainService = resolve(CabinetDomainService::class);

                $cabinetDomain = $cabinetDomainService->findDomainByHost($host);

                if (!$cabinetDomain) {
                    abort(404);
                }

                /** @var CabinetSiteService $siteService */
                $siteService = resolve(CabinetSiteService::class);
                $site = $siteService->findById($cabinetDomain->cabinet_site_id);

                if (!$cabinetDomain->isActive() || !$site->isActive()) {
                    $redirectUrl = urlWithCabinetRoot($site->cabinet, $request->path());
                    return redirect($redirectUrl);
                }

                config([
                    'session.domain' => $host,
                    'footprints.cookie_domain' => $host,
                ]);
            } else {
                /** @var CabinetSiteService $siteService */
                $siteService = resolve(CabinetSiteService::class);

                $subdomain = str_replace('.' . $rootDomain, '', $host);

                $site = $siteService->findBySlug($subdomain);

                if (!$site || !$site->isActive()) {
                    abort(404);
                }
            }

            $request->host = $host;
            $request->request_site = $site;
            $request->request_cabinet = cabinetById($site->cabinet_id);
        }

        return $next($request);
    }
}
