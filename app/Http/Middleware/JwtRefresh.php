<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
 
class JwtRefresh extends BaseMiddleware
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
        $token = JWTAuth::getToken();
        $new_token = JWTAuth::refresh($token);
        // try {
        //     $user = JWTAuth::parseToken()->authenticate();
        //   } catch (Exception $e) {
        //         if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
        //       return response()->json(['message' => 'Token is Invalid'], 403);
        //     }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
        //       return response()->json(['message' => 'Token is Expired'], 401);
        //     }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenBlacklistedException){
        //       return response()->json(['message' => 'Token is Blacklisted'], 400);
        //     }else{
        //           return response()->json(['message' => 'Authorization token not found'], 404);
        //     }
        //   }
    
          return $next($request);
    }
}
