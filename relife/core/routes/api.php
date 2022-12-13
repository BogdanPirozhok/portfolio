<?php

use App\Http\Controllers\Chat\ChatController;
use App\Http\Controllers\Chat\ChatMessageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['prefix' => 'v1'], function () {
    Route::middleware(['auth:api', 'can:is-verified', 'appVersion'])->group(function () {
        Route::apiResource('chats', ChatController::class)->only(['index', 'show', 'store', 'destroy']);

        Route::prefix('chats/{chat}')->group(function () {
            Route::put('sync-users', [ChatController::class, 'syncUsers']);
            Route::put('attach-user', [ChatController::class, 'attachUsers']);
            Route::get('users', [ChatController::class, 'getUsers']);

            Route::apiResource('chat-messages', ChatMessageController::class)->only(['index', 'store']);
            Route::post('chat-messages/set-read', [ChatMessageController::class, 'setRead']);
        });

        Route::prefix('chat-messages/{chatMessage}')->group(function () {
            Route::delete('/', [ChatMessageController::class, 'destroy']);
        });
    });
});
