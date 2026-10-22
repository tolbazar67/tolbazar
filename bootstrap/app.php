<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckUserMode;
use App\Http\Middleware\EnsureBranchAccess;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Registering middleware aliases for Laravel 11
        $middleware->alias([
        'mode'          => \App\Http\Middleware\CheckUserMode::class,
        'branch.access' => \App\Http\Middleware\EnsureBranchAccess::class,
        'seller.role'   => \App\Http\Middleware\EnsureSellerRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();