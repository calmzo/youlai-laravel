<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    public function handle($request, Closure $next) {
//        $request->setTrustedProxies($request->getClientIps()); //反向代理
        $response = $next($request);
        $response->header("Access-Control-Allow-Origin", "*");
        $response->Header("Access-Control-Allow-Methods", "POST, GET, PUT, OPTIONS, DELETE");
        $response->header('Access-Control-Allow-Headers', '*');
        $response->header('Access-Control-Allow-Credentials', 'false');
        return $response;
    }

}
