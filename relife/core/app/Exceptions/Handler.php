<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    protected function unauthenticated(
        $request,
        AuthenticationException $exception,
    ) {
        return response()->json(
            [
                'success' => false,
                'errors' => [__('auth.unauthenticated')],
            ],
            401,
        );
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            Log::error($e);
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof ValidationException) {
            $errors = $e->response;
            $code = 400;
        }
        if ($e instanceof AuthorizationException) {
            $errors = [__('auth.forbidden')];
            $code = 403;
        }
        if (
            $e instanceof NotFoundHttpException ||
            $e instanceof ModelNotFoundException
        ) {
            $errors = [__('main.404')];
            $code = 404;
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            $errors = [__('main.405')];
            $code = 405;
        }

        if (isset($errors) && isset($code)) {
            return response()->json(
                [
                    'errors' => $errors,
                ],
                $code,
            );
        }

        return parent::render($request, $e);
    }
}
