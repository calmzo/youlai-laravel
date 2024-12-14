<?php

namespace App\Http\Middleware;

use App\Exceptions\Token\ForbiddenException;
use App\Lib\Authenticator\Authenticator;
use App\Utils\CodeResponse;
use Closure;
use Illuminate\Http\Request;

class Permission
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
        $auth = (new Authenticator($request))->check();
        if (!$auth) {
            throw new ForbiddenException(CodeResponse::ACCESS_UNAUTHORIZED);
        }
        return $next($request);
    }
}
