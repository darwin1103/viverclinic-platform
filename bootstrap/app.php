<?php

use App\Http\Middleware\ConfigureLanguage;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(ConfigureLanguage::class);
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($e instanceof \Illuminate\Session\TokenMismatchException || ($e instanceof HttpException && $e->getStatusCode() === 419)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Your session has expired. Please log in again'
                    ], 419);
                }
                return redirect()->route('login')
                    ->with('info', 'Your session has expired. Please log in again');
            }
            return null;
        });
    })->create();
