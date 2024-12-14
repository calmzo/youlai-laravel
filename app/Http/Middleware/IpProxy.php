<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IpProxy
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
        $request->setTrustedProxies(['123.57.156.251', $request->server->get('REMOTE_ADDR')], Request::HEADER_X_FORWARDED_AWS_ELB); //ip信任
        return $next($request);
    }
}
