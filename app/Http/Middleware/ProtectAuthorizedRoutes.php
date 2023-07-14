<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProtectAuthorizedRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        //Validar o token antes de passar para controller

        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return response()->json(['status' => 'Token is invalid'], 401);
            } else if ($e instanceof TokenExpiredException) {
                return response()->json(['status' => 'Token is Expired'], 401);
            } else {
                return response()->json(['status' => 'Authorization Token not Found'], 401);
            }
        }

        return $next($request);
    }
}
