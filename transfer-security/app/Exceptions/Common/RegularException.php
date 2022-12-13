<?php

namespace App\Exceptions\Common;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class RegularException extends Exception
{
    public function report(): void
    {
        Log::info(json_encode(
            $this->getTrace()
        ));
    }

    public function render($request): JsonResponse
    {
        return response()->json(
            [
                'success' => 'false',
                'message' => $this->getMessage(),
            ]
        );
    }
}
