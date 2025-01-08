<?php

namespace App\Http\Middleware;

use App\Exceptions\Token\ForbiddenException;
use App\Utils\CodeResponse;
use Closure;
use Illuminate\Http\Request;

class BadSqlGrammar
{
    // 忽略列表
    protected $except = [
        'admin/system/auth/login',
        'admin/system/auth/logout',
        'admin/system/auth/refresh-token',
    ];

    public function handle(Request $request, Closure $next)
    {
        if ($this->inExceptArray($request)) {
            return $next($request);
        }
        if ($this->isDevEnvironment()) {
            if (in_array($request->method(), ['POST', 'PUT', 'DELETE'])) {
                // 处理 POST PUT DELETE请求
                throw new ForbiddenException(CodeResponse::FORBIDDEN_OPERATION);
            }
        }
        return $next($request);
    }

    protected function isDevEnvironment()
    {
        return app()->environment('dev');
    }

    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }
            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }
        return false;
    }
}
