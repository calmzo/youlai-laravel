<?php

namespace App\Http\Middleware;

use App\Utils\CodeResponse;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use App\Exceptions\BusinessException;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }

    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson() || in_array('admin', $guards) || in_array('wx', $guards)) {
            throw new BusinessException(CodeResponse::TOKEN_INVALID);
        }
        parent::unauthenticated($request, $guards);
    }
}
