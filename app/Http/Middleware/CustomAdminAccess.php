<?php

namespace App\Http\Middleware;

use App\Http\Controllers\BaseController;
use Closure;
use Database\Seeders\PermissionValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomAdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Vérifie si l'utilisateur est bien connecté dans la session Laravel
        $result_auth = BaseController::auth($request);
        if (! $result_auth['success']) {
            return $result_auth['response'];
        }

        $user = Auth::user();

        if (! $user->hasPermission(PermissionValue::ADMIN)) {
            return redirect('/');
        }

        return $next($request);
    }
}
