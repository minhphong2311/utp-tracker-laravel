<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateSecretKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $secret = $request->header('Authorization');
        if (is_null($secret) || in_array($secret, array(env('API_SECRET1', ''),env('API_SECRET2', ''))) == false ) {
            return response()->json([
                'success' => false,
                'message' => 'This resource needs to be authenticated'
            ], 401);
        }
        return $next($request);
    }
}
