<?php

namespace App\Services\System;

use App\Services\BaseService;
use App\Utils\Constant;
use App\Utils\RedisCache;

class PermissionService extends BaseService
{
    public function getRolePermsFormCache($roles)
    {
        if (empty($roles)){
            return [];
        }
        $rolePermsList = RedisCache::getInstance()->hMGet(Constant::ROLE_PERMS_PREFIX, $roles);
        $perms = [];
        foreach ($rolePermsList as $rolePermsObj) {
            $perms = array_merge($perms, $rolePermsObj ?: []);
        }
        return $perms;
    }
}
