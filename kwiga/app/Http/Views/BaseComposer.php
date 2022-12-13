<?php

namespace App\Http\Views;

use App\Models\Cabinet\Cabinet;
use App\Models\User\User;
use App\Services\Auth\AuthService;
use App\Services\Cabinet\CabinetService;
use App\Services\Common\CountryService;
use App\Services\Common\UrlLocalizer;
use App\Services\Payment\CurrencyService;
use App\Services\System\RouteService;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class BaseComposer
{

    /**
     * ProfileComposer constructor.
     * @param CabinetService $cabinetService
     */
    public function __construct(private readonly CabinetService $cabinetService)
    {
    }

    /**
     * Bind data to the view.
     *
     * @param View $view
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function compose(View $view): void
    {
        /** @var ?User $user */
        $user = auth()->user();

        /** @var Cabinet $cabinet */
        $cabinet = cabinet();

        $appDomain = config('app.domain');

        $appUrl = $cabinet
            ? currentLocaleCabinetRoute($cabinet, 'home.expert')
            : currentLocaleRoute('home');

        $appUrl = Str::finish($appUrl, '/');

        $appRootUrl = Str::finish(currentLocaleRoute('home'), '/');

        $appLocale = UrlLocalizer::getLocaleForUrl();
        $appCurrentLocale = App::getLocale();

        $appUrlNoLocale = $cabinet ? cabinetRoute($cabinet, 'home.expert') : route('home');

        $appSubdomain = optional($cabinet)->getSlug();

        $fileStorageUrl = getDomainFileStorage() . 'upload';
        $fileStorageUrlByModel = getDomainFileStorage() . 'upload-by-model';

        $token = optional(session('token'))->token ?? session('token')['token'] ?? null;

        if (is_null($token) && Auth::guard('web')->check() && !$user->isAdmin()) {
            /** @var AuthService $authService */
            $authService = resolve(AuthService::class);
            $token = $authService->createBearerToken($user)->token;
        }

        $activeCabinet = $this->cabinetService->getActiveCabinet();

        /** @var RouteService $routeService */
        $routeService = resolve(RouteService::class);
        $cachedRoutes = $routeService->getCachedRoutes();

        /** @var CurrencyService $currencyService */
        $currencyService = resolve(CurrencyService::class);
        $currentCurrency = $currencyService->getCurrentCurrency();

        $isOpenOnProd = true;

        $mainCabinet = $user ? $this->cabinetService->getUserMainCabinet($user) : null;
        if (app()->isProduction() && $mainCabinet && !$mainCabinet->is_active) {
            $isOpenOnProd = false;
        }

        /** @var CountryService $countryService */
        $countryService = resolve(CountryService::class);
        $geoData = $countryService->getDataByCurrentGeo();
        $country = $geoData['country'];

        $view->with(compact([
            'user',
            'appUrl',
            'appRootUrl',
            'appUrlNoLocale',
            'appSubdomain',
            'appLocale',
            'appCurrentLocale',
            'fileStorageUrl',
            'fileStorageUrlByModel',
            'appDomain',
            'token',
            'activeCabinet',
            'cachedRoutes',
            'currentCurrency',
            'isOpenOnProd',
            'country'
        ]));
    }
}
