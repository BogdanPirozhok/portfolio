<?php

namespace App\Exceptions;

use App\Exceptions\Common\NotFoundBlankException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Prettus\Validator\Exceptions\ValidatorException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ExceptionTrait
{
    public function apiException($request, $e): \Illuminate\Http\Response|JsonResponse|ResponseFactory|RedirectResponse
    {
        if ($this->isModelNotFound($e)) {
            Log::warning($e);
        }

        if ($this->isNotFoundBlank($e)) {
            return $this->notFoundBlankResponse($request);
        }

        if ($request->is('api/*') || $request->routeIs('public.*')) {
            if ($this->isModelNotFound($e)) {
                return $this->modelResponse($e);
            }

            if ($this->isNotFound($e)) {
                return $this->notFoundResponse($request);
            }

            if ($this->isQuery($e)) {
                return $this->queryResponse($e);
            }

            if ($this->isValidation($e)) {
                return $this->validationResponse($e);
            }

            if ($e instanceof AccessDeniedHttpException) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], $e->getStatusCode());
            }

            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], Response::HTTP_UNAUTHORIZED);
            }

            if ($e instanceof ValidatorException) {
                return response()->json([
                    'error' => true,
                    'message' => $e->getMessageBag(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

//        if ($e instanceof HttpException) {
//            return response()->json([
//                'error' => true,
//                'message' => 'Access denied.'
//            ], Response::HTTP_FORBIDDEN);
//        }
        }

        return parent::render($request, $e);
    }

    protected function isModelNotFound($exception): bool
    {
        return $exception instanceof ModelNotFoundException;
    }

    protected function isNotFound($exception): bool
    {
        return $exception instanceof NotFoundHttpException;
    }

    protected function isNotFoundBlank($exception): bool
    {
        return $exception instanceof NotFoundBlankException;
    }

    protected function isQuery($exception): bool
    {
        return $exception instanceof QueryException;
    }

    protected function isValidation($exception): bool
    {
        return $exception instanceof ValidationException;
    }

    protected function modelResponse($exception): \Illuminate\Http\Response|ResponseFactory
    {
        $modelClass = explode('\\', $exception->getModel());

        $data = [
            'error' => 'Model ' . end($modelClass) . ' not found',
        ];

        if (app()->isLocal()) {
            $data['trace'] = $exception->getTrace();
        }

        return response($data, Response::HTTP_NOT_FOUND);
    }

    protected function notFoundResponse(Request $request): \Illuminate\Http\Response|ResponseFactory
    {
        if ($request->wantsJson()) {
            return response([
                'error' => 'Incorrect route name',
            ], Response::HTTP_NOT_FOUND);
        }

        return response(
            view('errors.404'),
            Response::HTTP_NOT_FOUND
        )->header('Content-type', 'text/html');
    }

    protected function notFoundBlankResponse(Request $request): \Illuminate\Http\Response|ResponseFactory
    {
        if ($request->wantsJson()) {
            return response([
                'error' => 'Incorrect route name',
            ], Response::HTTP_NOT_FOUND);
        }

        return response(
            view('errors.404_blank'),
            Response::HTTP_NOT_FOUND
        )->header('Content-type', 'text/html');
    }

    protected function queryResponse(QueryException $e): \Illuminate\Http\Response|ResponseFactory
    {
        if (property_exists($e, 'errorInfo') && count($e->errorInfo) > 1) {
            $errorInfo = $e->errorInfo[2];
        } else {
            $errorInfo = $e->getMessage();
        }
        
        $error = [
            'error' => 'Incorrect query in database. ' . $errorInfo,
        ];

        if (app()->isLocal()) {
            $error['message'] = $e->getMessage();
            $error['bindings'] = $e->getBindings();
            $error['trace'] = $e->getTrace();
        }
        return response($error, Response::HTTP_NOT_FOUND);
    }

    protected function validationResponse($exception): JsonResponse
    {
        return response()->json([
            'message' => $exception->getMessage(),
            'errors'  => $exception->errors(),
        ], $exception->status);
    }
}
