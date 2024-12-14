<?php

namespace App\Lib\Authenticator\Executor;

interface IExecutor
{
    public function handle($roles = [], $permissionName = ''): bool;
}
