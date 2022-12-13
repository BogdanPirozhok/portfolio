<?php

namespace App\Helpers;

use App\Models\Cabinet\Cabinet;
use App\Services\Common\UrlLocalizer;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Pure;

class UrlHelper
{

    final const ROUTE_NAME_LOCALE_PREFIX = 'locale.';

    /**
     * Check if $routeName is $name or localized variant of $name
     */
    public static function routeNameIs(?string $routeName, string $name): bool
    {
        return $routeName === $name || $routeName === UrlHelper::ROUTE_NAME_LOCALE_PREFIX . $name;
    }

    /**
     * Check if route name is localized route
     */
    #[Pure]
    public static function isLocaleRoute(string $routeName): bool
    {
        return Str::startsWith($routeName, UrlHelper::ROUTE_NAME_LOCALE_PREFIX);
    }

    /**
     * Check if route name has localized variant
     */
    public static function hasLocaleRoute(string $routeName): bool
    {
        return Route::has(UrlHelper::ROUTE_NAME_LOCALE_PREFIX . $routeName);
    }

    /**
     * Get route with lang prefix by current locale if route can be localized
     */
    public static function currentLocaleRoute(string $routeName, mixed $parameters = [], bool $absolute = true): string
    {
        $parameters['locale'] = App::getLocale();

        return UrlHelper::localeRoute($routeName, $parameters, $absolute);
    }

    /**
     * Get route with lang prefix by parameters['locale'] if route can be localized
     */
    public static function localeRoute(string $routeName, mixed $parameters = [], bool $absolute = true): string
    {
        $isLocaleRoute = false;

        if (!UrlHelper::isLocaleRoute($routeName) && UrlHelper::hasLocaleRoute($routeName)) {
            $routeName = UrlHelper::ROUTE_NAME_LOCALE_PREFIX . $routeName;
            $isLocaleRoute = true;
        } elseif (UrlHelper::isLocaleRoute($routeName)) {
            $isLocaleRoute = true;
        }

        $parameters = UrlHelper::clearParameters($parameters, $isLocaleRoute);

        if (isset($parameters['locale'])) {
            $parameters['locale'] = UrlLocalizer::getRoutePrefixByLocale($parameters['locale']);
        }

        $parameters = self::checkDomainParameters($routeName, $parameters, $absolute);

        return route($routeName, $parameters, $absolute);
    }

    /**
     * Clear unnecessary params for route
     *
     * @param mixed $parameters
     */
    public static function clearParameters(array $parameters, bool $isLocaleRoute = false): array
    {
        if (!$isLocaleRoute) {
            unset($parameters['locale']);
        }

        $defaultLocale = config('voyager.multilingual.default');

        if (isset($parameters['locale']) && $defaultLocale === $parameters['locale']) {
            unset($parameters['locale']);
        }

        return $parameters;
    }

    /**
     * Get route with force root domain or $root parameter
     */
    public static function routeWithForcedRoot(
        array|string $name,
        mixed $parameters = [],
        bool $absolute = true,
        ?string $root = null
    ): string {
        self::setDefaultRoot($root, $parameters);
        $parameters = self::checkDomainParameters($name, $parameters, $absolute);

        url()->forceRootUrl($root);
        $url = route($name, $parameters, $absolute);
        url()->forceRootUrl(null);

        return $url;
    }

    /**
     * Get route with force root domain or $root parameter
     * and with lang prefix by parameters['locale'] if route can be localized
     */
    public static function localeRouteWithForcedRoot(
        array|string $name,
        mixed $parameters = [],
        bool $absolute = true,
        ?string $root = null
    ): string {
        self::setDefaultRoot($root, $parameters);
        $parameters = self::checkDomainParameters($name, $parameters, $absolute);

        url()->forceRootUrl($root);
        $url = localeRoute($name, $parameters, $absolute);
        url()->forceRootUrl(null);

        return $url;
    }

    /**
     * Get route with force root domain or $root parameter
     * and with lang prefix by current locale if route can be localized
     */
    public static function currentLocaleRouteWithForcedRoot(
        array|string $name,
        mixed $parameters = [],
        bool $absolute = true,
        ?string $root = null
    ): string {
        $parameters['locale'] = App::getLocale();

        return localeRouteWithForcedRoot($name, $parameters, $absolute, $root);
    }

    /**
     * Get route with forced cabinet subdomain
     */
    public static function cabinetRoute(
        Cabinet $cabinet,
        array|string $name,
        mixed $parameters = [],
        bool $absolute = true
    ): string {
        $parameters['domain'] = $cabinet->getDomain();

        return route($name, $parameters, $absolute);
    }

    /**
     * Get route with forced cabinet subdomain and with lang prefix by parameters['locale'] if route can be localized
     */
    public static function localeCabinetRoute(
        ?Cabinet $cabinet,
        array|string $name,
        mixed $parameters = [],
        bool $absolute = true
    ): string {
        $parameters['domain'] = optional($cabinet)->getDomain();

        return localeRoute($name, $parameters, $absolute);
    }

    /**
     *  Get route with forced cabinet subdomain and with current lang prefix
     */
    public static function currentLocaleCabinetRoute(
        Cabinet $cabinet,
        array|string $name,
        mixed $parameters = [],
        bool $absolute = true
    ): string {
        $parameters['domain'] = $cabinet->getDomain();
        $parameters['locale'] = App::getLocale();

        return localeRoute($name, $parameters, $absolute);
    }

    /**
     * Get url with forced root or $root parameter
     */
    public static function urlWithForcedRoot(
        ?string $path = null,
        mixed $parameters = [],
        bool $secure = null,
        ?string $root = null
    ): string {
        url()->forceRootUrl($root);
        $url = url($path, $parameters, $secure);
        url()->forceRootUrl(null);

        return $url;
    }

    /**
     * Get url with forced cabinet subdomain
     */
    public static function urlWithCabinetRoot(
        Cabinet $cabinet,
        ?string $path = null,
        mixed $parameters = [],
        bool $secure = null
    ): string {
        return UrlHelper::urlWithForcedRoot($path, $parameters, $secure, $cabinet->link);
    }

    /**
     * @param string|null $url
     * @param bool $isNotice
     * @return string|null
     */
    public static function getYoutubeVideoId(?string $url, bool $isNotice = false): ?string
    {
        if (!$url) {
            return $url;
        }

        $hasMatches = preg_match(
            '/^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?]*).*/',
            $url,
            $matches
        );

        if ($hasMatches) {
            return $matches[1];
        }

        if ($isNotice) {
            Log::warning('Unrecognizable youtube link found: '. $url);
        }

        return $url;
    }

    private static function checkDomainParameters(string $routeName, array $parameters, bool $absolute): array
    {
        $newParameters = $parameters;

        if (!isset($newParameters['domain'])) {
            $newParameters['domain'] = request()->getHost();
        }

        $route = route($routeName, $newParameters, $absolute);

        if (strripos($route, 'domain=') !== false) {
            unset($newParameters['domain']);
        }

        return $newParameters;
    }

    private static function setDefaultRoot(?string &$root = null, ?array &$parameters = [])
    {
        if (!$root) {
            /** @var Cabinet $site */
            $cabinet = cabinet();

            if ($cabinet) {
                $root = $cabinet->getUsedDomain();
            }
        }

        $root ??= config('app.domain');
        $parameters['domain'] = $root;

        $httpProtocol = request()->getScheme();
        $httpPort = explode(':', request()->server->get('HTTP_HOST'))[1] ?? 80;

        $root = $httpProtocol .'://'. $root;
        if (app()->isLocal() && !in_array($httpPort, [443, 80])) {
            $root .= ':' . $httpPort;
        }
    }

    public static function retrieveQueryParamsFromUrl(string $url): array
    {
        $queryArray = [];
        $query = parse_url($url, PHP_URL_QUERY);
        $query = explode('&', $query);

        foreach ($query as $row) {
            $parsedRow = explode('=', $row, 2);

            if (count($parsedRow) === 2) {
                //TODO: needs to handle array prams also. Like test[]=1&test[]=2
                $queryArray[$parsedRow[0]] = $parsedRow[1];
            }
        }
        return $queryArray;
    }
}
