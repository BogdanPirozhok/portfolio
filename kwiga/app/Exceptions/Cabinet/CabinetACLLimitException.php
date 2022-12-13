<?php

namespace App\Exceptions\Cabinet;

use App\Enums\ErrorTypeEnum;
use Illuminate\Http\Response;

/**
 * Exception thrown when has reached the tariff limit
 */
class CabinetACLLimitException extends \Exception
{
    public function report(): void
    {
    }

    public function render($request)
    {
        $cabinet = cabinet();

        $message = lang('error.exception.tariff_limit', null, [
            'type' => lang('pricing.limit.type_plural.' . $this->message),
            'change_tariff_url' => $cabinet ? currentLocaleCabinetRoute($cabinet, 'expert.any', [
                'any' => 'settings/change-tariff'
            ]) : '#',
            'support_url' => 'mailto:support@kwiga.com'
        ]);

        if ($request->wantsJson()) {
            return response()->json(
                [
                    'success' => 'false',
                    'error_type' => ErrorTypeEnum::E_TYPE_TARIFF_LIMIT,
                    'message' => $message,
                ],
                Response::HTTP_FORBIDDEN
            );
        } else {
            return response(
                view('errors.403-tariff', compact('message')),
                Response::HTTP_FORBIDDEN
            )->header('Content-type', 'text/html');
        }
    }
}
