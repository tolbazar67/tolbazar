<?php

use App\Http\Middleware\AdminIpWhitelist;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckUserMode;
use App\Http\Middleware\EnsureAccountActive;
use App\Http\Middleware\EnsureBranchAccess;
use App\Http\Middleware\EnsureSellerRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'mode'          => CheckUserMode::class,
            'branch.access' => EnsureBranchAccess::class,
            'seller.role'   => EnsureSellerRole::class,
            'append'          => EnsureAccountActive::class,
            'admin.ip'      => AdminIpWhitelist::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {

            $status = $e->getStatusCode();

            if (!view()->exists("errors.{$status}")) {
                return null; // fall back to Laravel default
            }

            if ($request->expectsJson()) {
                return null;
            }

            return response()->view("errors.{$status}", [
                'exception' => $e,
                'message'   => $e->getMessage() ?: null,
            ], $status, $e->getHeaders());
        });

    })->create();
