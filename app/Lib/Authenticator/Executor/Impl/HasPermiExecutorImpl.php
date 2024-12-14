<?php

namespace App\Lib\Authenticator\Executor\Impl;

use App\Lib\Authenticator\Executor\IExecutor;
use App\Services\System\PermissionService;

class HasPermiExecutorImpl implements IExecutor
{

    public function handle($roles = [], $permission = ''): bool
    {
        $permissions = PermissionService::getInstance()->getRolePermsFormCache($roles);
        return in_array($permission, $permissions);
    }
}
