<?php

namespace App\Services\System;

use App\Enums\MenuEnum;
use App\Models\System\SysRole as Role;
use App\Models\System\SysRoleMenu as RoleMenu;
use App\Services\BaseService;
use App\Utils\Constant;
use App\Utils\RedisCache;

class RoleMenuService extends BaseService
{
    public function listMenuIdsByRoleId($roleId)
    {
        $menuIds = RoleMenu::query()->where('role_id', $roleId)->get();
        return $menuIds;
    }


    /**
     * 刷新权限缓存 (角色编码变更时调用)
     *
     * @return bool
     * @author 2024/6/19 10:48
     */
    public function refreshRolePermsCacheChange($oldRoleCode, $newRoleCode)
    {
        $redis = RedisCache::getInstance();
        RedisCache::getInstance()->hdel(Constant::ROLE_PERMS_PREFIX, $oldRoleCode);

        $list = $this->getRolePermsList($newRoleCode);
        foreach ($list as $item) {
            $perms = $item['perms'];
            $redis->hset(Constant::ROLE_PERMS_PREFIX, $item['role_code'], $perms);
        }
        return true;
    }


    /**
     * 刷新权限缓存
     *
     * @param null $roleCode
     * @return bool
     * @author 2024/6/19 10:58
     */
    public function refreshRolePermsCache($roleCode = null)
    {
        $redis = RedisCache::getInstance();
        if ($roleCode) {
            RedisCache::getInstance()->delete(Constant::ROLE_PERMS_PREFIX . $roleCode);
        } else {
            $redis->deleteObject(Constant::ROLE_PERMS_PREFIX.'*');
        }

        $list = $this->getRolePermsList($roleCode);
        foreach ($list as $item) {
            $perms = $item['perms'];
            $redis->hset(Constant::ROLE_PERMS_PREFIX, $item['role_code'], $perms);
        }
        return true;
    }


    /**
     * 获取权限和拥有权限的角色列表
     *
     * @param $code
     * @return void
     * @author 2024/6/11 11:28
     */
    public function getRolePermsList($code = null)
    {
        $query = Role::query();
        if ($code) {
            $query->where('code', $code);
        }
        $roleList = $query
            ->whereHasIn('menus', function ($query) {
                $query->where('type', MenuEnum::TYPE_BUTTON);
            })
            ->with([
                'menus' => function ($query) {
                    $query->where('type', MenuEnum::TYPE_BUTTON)->select('id', 'perm');
                }])
            ->get(['id' , 'code']);

        $rolePermsList = [];
        foreach ($roleList as $role) {
            $rolePermsList[] = [
                'role_code' => $role->code ?? '',
                'perms'      => $role->menus->pluck('perm')->toArray(),
            ];
        }
        return $rolePermsList;
    }


}
