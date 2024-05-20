<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;


class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try{
            $jwt = $request->bearerToken();

            if($jwt == null || $jwt == '') {
                return response()->json([
                    'msg' => 'Token must be filled'
                ], );
            }
            $decoded = JWT::decode($jwt, new Key(env('JWT_SECRET_KEY'), 'HS256'));
            if($decoded->role == 'admin' || $decoded->role == 'user') {
                // ambil email dari decode
                $request['email']= $decoded->email;
                return $next($request);
            }
            return response()->json([
                'msg' => 'Unauthorized'
            ], 401 );

        }catch(ExpiredException $e){
            return response()->json([
                $e -> getMessage()
            ], 401);
        }
    }
}
