<?php

namespace App\Lib\Authenticator;

use App\Lib\Authenticator\Executor\IExecutor;
use App\Lib\Authenticator\Executor\Impl\HasPermiExecutorImpl;
use App\Lib\Authenticator\Executor\Impl\LoginRequireExecutorImpl;
use App\Lib\Authenticator\Executor\Impl\AdminRequireExecutorImpl;
use App\Lib\Authenticator\Executor\Impl\GroupRequireExecutorImpl;
use App\Enums\PermissionLevelEnums;

class AuthenticatorExecutorFactory
{
    public static function getInstance(string $method): IExecutor
    {
        $instance = null;
        switch ($method) {
            case "hasPermi":
                //验证用户是否具备某权限
                $instance = new HasPermiExecutorImpl();
                break;
            case "lacksPermi":
                //todo 验证角色是否不具备某权限，与hasPermi逻辑相反
                break;
            case "hasAnyPermi":
                //todo 验证角色是否具有以下任意一个权限 个逗号分隔
                break;
        }
        return $instance;
    }
}
