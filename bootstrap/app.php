<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Throwable;

require_once __DIR__.'/show_errors.php';

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! karnacab_debug_wanted()) {
                return null;
            }

            return response(
                karnacab_error_html(
                    $e::class,
                    $e->getMessage() !== '' ? $e->getMessage() : '(no message)',
                    $e->getFile(),
                    $e->getLine(),
                    $e->getTraceAsString(),
                ),
                500,
            )->header('Content-Type', 'text/html; charset=utf-8');
        });
    })->create();
