<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User $auth */
        $auth = Auth::user();
        if ($auth) {
            if (!$auth->hasAnyRole(['admin', 'super-admin'])) {
                abort(403, 'Access Denied');
            }
        }
        return $next($request);
    }
}
