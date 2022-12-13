<?php

namespace App\Http;

use App\Http\Middleware\ActiveCabinet;
use App\Http\Middleware\ApiAuthorizationByToken;
use App\Http\Middleware\ApiSetLocale;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\AuthorizationByHash;
use App\Http\Middleware\CabinetApiChecker;
use App\Http\Middleware\CheckExecutionTime;
use App\Http\Middleware\CustomRequestLimiter;
use App\Http\Middleware\DecodeRsaEncrypted;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\GetCabinetByHash;
use App\Http\Middleware\GetDomain;
use App\Http\Middleware\HasActionsOnDomains;
use App\Http\Middleware\HasCabinet;
use App\Http\Middleware\LastActivity;
use App\Http\Middleware\MailTrackClicks;
use App\Http\Middleware\Moderator;
use App\Http\Middleware\NoAdmin;
use App\Http\Middleware\NoModerator;
use App\Http\Middleware\OnlyActiveDomain;
use App\Http\Middleware\PreventRequestsDuringMaintenance;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RedirectIfAuthenticatedUser;
use App\Http\Middleware\RedirectNewDomain;
use App\Http\Middleware\RouteArgumentsFilterMiddleware;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\SetLocaleBySession;
use App\Http\Middleware\TrackFirstReferer;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Middleware\VisitorMiddleware;
use Fruitcake\Cors\HandleCors;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Passport\Http\Middleware\CheckForAnyScope;
use Laravel\Passport\Http\Middleware\CheckScopes;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        RedirectNewDomain::class,
        TrustProxies::class,
        HandleCors::class,
        PreventRequestsDuringMaintenance::class,
        ValidatePostSize::class,
        TrimStrings::class,
        ConvertEmptyStringsToNull::class,
        CheckExecutionTime::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            AuthorizationByHash::class,
            GetDomain::class,
            SubstituteBindings::class,
            MailTrackClicks::class,
            SetLocaleBySession::class,
            TrackFirstReferer::class,
            LastActivity::class,
            // CaptureAttributionDataMiddleware::class, //Footprint visitors default library logic
        ],

        'api' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            'throttle:api',
            SubstituteBindings::class,
            ApiSetLocale::class,
            GetDomain::class,
            LastActivity::class,
        ],

        'public_api' => [
            SubstituteBindings::class,
            'cabinet.by.hash',
            'user.auth.by.token',
            'custom.throttle:0.016,10',
            ApiSetLocale::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => Authenticate::class,
        'auth.basic' => AuthenticateWithBasicAuth::class,
        'cache.headers' => SetCacheHeaders::class,
        'can' => Authorize::class,
        'guest' => RedirectIfAuthenticated::class,
        'no.auth.user' => RedirectIfAuthenticatedUser::class,
        'password.confirm' => RequirePassword::class,
        'signed' => ValidateSignature::class,
        'throttle' => ThrottleRequests::class,
        'verified' => EnsureEmailIsVerified::class,
        'has.cabinet' => HasCabinet::class,
        'only.active.domain' => OnlyActiveDomain::class,
        'has.actions.domains' => HasActionsOnDomains::class,
        'locale' => SetLocale::class,
        'no.admin' => NoAdmin::class,
        'no.moderator' => NoModerator::class,
        'api.set.locale' => ApiSetLocale::class,
        'moderator' => Moderator::class,
        'cabinet.active' => ActiveCabinet::class,
        'filter.route' => RouteArgumentsFilterMiddleware::class,
        'visitors' => VisitorMiddleware::class,
        'scopes' => CheckScopes::class,
        'scope' => CheckForAnyScope::class,
        'cabinet.by.hash' => GetCabinetByHash::class,
        'cabinet.api.check' => CabinetApiChecker::class,
        'custom.throttle' => CustomRequestLimiter::class,
        'decode.encrypted' => DecodeRsaEncrypted::class,
        'user.auth.by.token' => ApiAuthorizationByToken::class,
    ];
}
