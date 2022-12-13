<?php

use App\Http\Controllers\Cabinet\CabinetDomainController;
use App\Http\Controllers\Cabinet\CabinetSiteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api'])->group(
    static function () {
        Route::prefix('cabinet')
            ->middleware('has.cabinet')
            ->group(function () {
                Route::prefix('sites')->group(function () {
                    Route::get('/', [CabinetSiteController::class, 'index']);
                    Route::post('/', [CabinetSiteController::class, 'store']);

                    Route::prefix('{site}')->group(function () {
                        Route::get('/', [CabinetSiteController::class, 'show']);

                        Route::put('delete', [CabinetSiteController::class, 'delete']);
                        Route::put('restore', [CabinetSiteController::class, 'restore']);

                        Route::put('set-default', [CabinetSiteController::class, 'setDefault']);
                        Route::put('set-active', [CabinetSiteController::class, 'setActive']);

                        Route::group(['middleware' => 'has.actions.domains'], function () {
                            Route::apiResource('domains', CabinetDomainController::class)
                                ->only(['store']);
                        });
                    });

                    Route::prefix('domains/{domain}')->group(function () {
                        Route::delete('/', [CabinetDomainController::class, 'destroy']);

                        Route::put('set-confirm', [CabinetDomainController::class, 'setConfirm']);
                        Route::put('change-site', [CabinetDomainController::class, 'changeSite']);
                    });
                });
            });
    }
);
