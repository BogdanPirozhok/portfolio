<?php

namespace App\Exceptions\Cabinet;

use App\Enums\ErrorTypeEnum;
use Illuminate\Http\Response;

/**
 * Exception thrown when user does not have permissions
 */
class CabinetACLException extends \Exception
{
    public function report(): void
    {
    }

    public function render($request)
    {
        $message = null;

        if (!empty($this->message)) {
            $message = lang('error.exception.cabinet_acl_denied_permissions', null, [
                'permissions' => $this->message
            ]);
        }

        if ($request->is('api/*') || $request->routeIs('public.*')) {
            return response()->json(
                [
                    'success' => 'false',
                    'error_type' => ErrorTypeEnum::E_TYPE_FORBIDDEN,
                    'message' => $message,
                ],
                Response::HTTP_FORBIDDEN
            );
        } else {
            return response(
                view('errors.403', compact('message')),
                Response::HTTP_FORBIDDEN
            )->header('Content-type', 'text/html');
        }
    }
}
