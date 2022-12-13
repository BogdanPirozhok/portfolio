<?php

use App\Http\Controllers\InitController;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('logs', [LogViewerController::class, 'index']);

Route::post(config('telegram.bots.ts.token') . '/update', [InitController::class, 'update']);

Route::get('{token}/set-webhook', [InitController::class, 'setWebhook']);
Route::get('{token}/remove-webhook', [InitController::class, 'removeWebhook']);
