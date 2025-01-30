<?php

use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $exception, $request) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access. Please provide a valid token.',
            ], 401);
        });

        $exceptions->render(function (ValidationException $exception, $request) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $exception->errors(),
            ], 422);
        });

        $exceptions->render(function (HttpResponseException $exception, $request) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], $exception->getStatusCode() ?? 500);
        });

        $exceptions->render(function (MethodNotAllowedHttpException $exception, $request) {
            return response()->json([
                'status' => 'error',
                'message' => 'Method Not Allowed.',
            ], 405);
        });
        
    })->create();
