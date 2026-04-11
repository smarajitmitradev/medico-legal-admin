<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

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
                'error' => ['code' => 'TOKEN_INVALID']
            ], 401);
        }

        // ✅ Token expired
        if ($exception instanceof TokenExpiredException) {
            return response()->json([
                'success' => false,
                'message' => 'Token expired',
                'error' => ['code' => 'TOKEN_EXPIRED']
            ], 401);
        }

        // ✅ Token missing
        if ($exception instanceof JWTException) {
            return response()->json([
                'success' => false,
                'message' => 'Token missing',
                'error' => ['code' => 'TOKEN_MISSING']
            ], 401);
        }

        // ✅ Unauthorized
        if ($exception instanceof UnauthorizedHttpException) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
                'error' => ['code' => 'UNAUTHORIZED']
            ], 401);
        }

        // ✅ Validation error
        if ($exception instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $exception->errors()
            ], 422);
        }

        // ✅ 404 Not Found
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'success' => false,
                'message' => 'API endpoint not found'
            ], 404);
        }

        // ✅ Method Not Allowed (POST instead of GET etc.)
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'success' => false,
                'message' => 'HTTP method not allowed'
            ], 405);
        }

        // ✅ Database error (duplicate entry etc.)
        if ($exception instanceof QueryException) {
            return response()->json([
                'success' => false,
                'message' => 'Database error (possible duplicate entry)'
            ], 400);
        }

        // ✅ Fallback (any other error)
        return response()->json([
            'success' => false,
            'message' => $exception->getMessage() ?: 'Server Error'
        ], 500);
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
