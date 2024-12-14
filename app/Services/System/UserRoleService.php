<?php

namespace App\Services\System;

use App\Models\System\SysRole as Role;
use App\Services\BaseService;

class UserRoleService extends BaseService
{

    public function hasAssignedUsers($roleId) {
        $role = Role::query()->where('id', $roleId)->first();
        $count = $role->users->count();
        return $count > 0;
    }

}
