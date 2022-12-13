<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AvailableException extends Exception
{
    public function report()
    {
        //
    }

    public function render(): JsonResponse
    {
        return response()->json(
            [
                'errors' => [$this->getMessage()],
            ],
            403
        );
    }
}
