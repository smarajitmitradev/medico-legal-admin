<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Render exceptions to JSON for API routes
     */
    public function render($request, Throwable $exception)
    {
        // ✅ Token invalid
        if ($exception instanceof TokenInvalidException) {
            return response()->json([
                'success' => false,
                'message' => 'Token is invalid',
                'error' => [
                    'code' => 'TOKEN_INVALID'
                ]
            ], 401);
        }

        // ✅ Token expired
        if ($exception instanceof TokenExpiredException) {
            return response()->json([
                'success' => false,
                'message' => 'Token expired',
                'error' => [
                    'code' => 'TOKEN_EXPIRED'
                ]
            ], 401);
        }

        // ✅ Token missing / general JWT error
        if ($exception instanceof JWTException) {
            return response()->json([
                'success' => false,
                'message' => 'Token missing',
                'error' => [
                    'code' => 'TOKEN_MISSING'
                ]
            ], 401);
        }

        // ✅ UnauthorizedHttpException fallback
        if ($exception instanceof UnauthorizedHttpException) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
                'error' => [
                    'code' => 'UNAUTHORIZED'
                ]
            ], 401);
        }

        return parent::render($request, $exception);
    }

    /**
     * Handle unauthenticated API requests
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated',
            'error' => [
                'code' => 'TOKEN_MISSING_OR_INVALID'
            ]
        ], 401);
    }
}
