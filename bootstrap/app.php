<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    // Traefik (Reverse Proxy) termine le SSL et forward les requêtes à Laravel (dans son container) en HTTP interne.
    // Sans ça, Laravel voit la requête en HTTP et génère des URLs en http://,
    // ce qui cause des erreurs de mixed content dans le navigateur.
    // trustProxies lui dit de lire le header X-Forwarded-Proto ajouté par Traefik,
    // pour qu'il sache que la requête originale était bien en HTTPS et génère ses URLs en https://.
    // source: https://laravel.com/docs/11.x/requests#configuring-trusted-proxies
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
