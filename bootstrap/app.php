<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // HAPUS api.php KARENA TIDAK ADA & TIDAK DIPAKAI
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ALIAS PDF TETAP ADA
        $middleware->alias([
            'Pdf' => Barryvdh\DomPDF\Facade\Pdf::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();