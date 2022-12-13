<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class RegularException extends Exception
{
    public function report()
    {
        //
    }

    public function render()
    {
        return response()->json(
            [
                'errors' => [$this->getMessage()],
            ],
            $this->getCode() ?: 422,
        );
    }
}
