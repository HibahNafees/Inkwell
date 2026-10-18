<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // The public URL is a Vercel rewrite that proxies to this server, so
        // build links from X-Forwarded-Host/Proto. Only these hosts are
        // accepted, so a spoofed forwarded host can't poison generated URLs.
        $middleware->trustProxies(at: '*');
        $middleware->trustHosts(at: ['hibah-inkwell.vercel.app', '66-135-1-187.sslip.io'], subdomains: false);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
