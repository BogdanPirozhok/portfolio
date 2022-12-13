<?php

namespace App\Http\Controllers;

use App\Exceptions\Common\RegularException;
use App\Services\InitService;
use Exception;
use Illuminate\Http\JsonResponse;
use Telegram\Bot\Laravel\Facades\Telegram;

class InitController extends Controller
{
    /**
     * @param string $token
     * @return JsonResponse
     * @throws RegularException
     */
    public function setWebhook(string $token): JsonResponse
    {
        $botConfig = config('telegram.bots.ts');

        if ($token === $botConfig['token']) {
            try {
                Telegram::setWebhook([
                    'url' => $botConfig['webhook_url'] . $botConfig['token'] . '/update'
                ]);

                return response()->json([
                    'success' => true
                ]);
            } catch (Exception $e) {
                throw new RegularException($e);
            }
        } else {
            throw new RegularException('Token is invalid!', 400);
        }
    }

    /**
     * @param string $token
     * @return JsonResponse
     * @throws RegularException
     */
    public function removeWebhook(string $token): JsonResponse
    {
        $botConfig = config('telegram.bots.ts');

        if ($token === $botConfig['token']) {
            try {
                Telegram::removeWebhook();

                return response()->json([
                    'success' => true
                ]);
            } catch (Exception $e) {
                throw new RegularException($e);
            }
        } else {
            throw new RegularException('Token is invalid!', 400);
        }
    }

    /**
     * @return void
     * @throws RegularException
     */
    public function update(): void
    {
        try {
            new InitService();
        } catch (Exception $e) {
            throw new RegularException($e);
        }
    }
}
