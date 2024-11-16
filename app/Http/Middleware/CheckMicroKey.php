<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMicroKey
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requiredKey = env('MICRO_KEY');
        if (!($request->hasHeader($requiredKey) &&   $request->header($requiredKey) == $requiredKey)) {
            return response()->json([
                'message' => 'Forbidden: Missing required header key.'
            ], 403);
        }
        return $next($request);
    }
}
