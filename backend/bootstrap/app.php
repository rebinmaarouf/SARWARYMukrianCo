<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                
                // Customize validation errors
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'message' => 'داتای داخڵکراو هەڵەیە',
                        'errors' => $e->errors(),
                    ], 422);
                }

                // Generic error for everything else in production
                return response()->json([
                    'message' => config('app.debug') ? $e->getMessage() : 'هەڵەیەکی ناوخۆیی ڕوویدا، تکایە پەیوەندی بە بەشی تەکنیکی بکە',
                    'error_code' => $status,
                    'debug_info' => config('app.debug') ? [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => collect($e->getTrace())->take(5)
                    ] : null
                ], $status);
            }
        });
    })->create();
