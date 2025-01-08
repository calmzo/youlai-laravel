<?php

namespace App\Http\Middleware;

use App\Exceptions\Token\ForbiddenException;
use App\Utils\CodeResponse;
use Closure;
use Illuminate\Http\Request;

class BadSqlGrammar
{

    public function handle(Request $request, Closure $next)
    {
        if ($this->isDevEnvironment()) {
            if (in_array($request->method(), ['POST', 'PUT', 'DELETE'])) {
                // 处理 PUT 请求
                throw new ForbiddenException(CodeResponse::FORBIDDEN_OPERATION);
            }
        }
        return $next($request);
    }

    protected function isDevEnvironment()
    {
        return app()->environment('dev');
    }
}
