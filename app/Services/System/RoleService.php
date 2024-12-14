<?php

namespace App\Services\System;

use App\Inputs\Admin\System\RoleFormInput;
use App\Inputs\Admin\System\RolePageInput;
use App\Models\System\SysRole as Role;
use App\Services\BaseService;
use App\Tools\Helpers;
use App\Utils\CodeResponse;
use App\Utils\Constant;
use App\Utils\RedisCache;

class RoleService extends BaseService
{
    public function getRoles($uid)
    {
        $role = Role::query()->where('user_id', $uid)->first();
    }


    public function isRoot($roles)
    {
        return in_array('ROOT', $roles);
    }

    /**
     * 获取角色的菜单ID集合
     *
     * @param $roleId
     * @return \Illuminate\Support\Collection
     * @author 2024/6/11 11:06
     */
    public function getRoleMenuIds($roleId)
    {
        $role  = Role::query()->where('id', $roleId)->with('menus')->first();
        $menus = $role->menus->pluck('id');
        return $menus;
    }


    /**
     * 修改角色的资源权限
     *
     * @param $roleId 角色ID
     * @param $menuIds 菜单ID集合
     * @return bool
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/19 15:28
     */
    public function assignMenusToRole($roleId, $menuIds)
    {

        $role = Role::query()->where('id', $roleId)->first();
        if (is_null($role)) {
            $this->throwBusinessException();
        }

        // 新增角色菜单
        if ($menuIds) {
            $role->menus()->sync($menuIds);
        }

        // 刷新角色的权限缓存
        RoleMenuService::getInstance()->refreshRolePermsCache($role->code);

        return true;
    }

    /**
     * 角色分页列表
     *
     * @param RolePageInput $input
     * @param string[] $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @author 2024/6/19 14:18
     */
    public function getRolePage(RolePageInput $input, $columns = ['*'])
    {
        $query  = Role::query();
        $query  = $query->when($input->keywords, function ($query) use ($input) {
            $query->where('name', 'like', "%{$input->keywords}%")->whereOr('code', 'like', "%{$input->keywords}%");
        });
        $isRoot = LoginService::getInstance()->isRoot();
        $query  = $query->when(!$isRoot, function ($query) {
            $query->where('code', '<>', Constant::ROOT_ROLE_CODE);
        });

        $rolePage = $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->pageSize, $columns, 'page', $input->pageNum);
        return $rolePage;
    }


    /**
     * 角色下拉列表
     *
     * @return \App\Models\BaseModel[]|array|\Illuminate\Database\Concerns\BuildsQueries[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|\Illuminate\Support\HigherOrderWhenProxy[]|\Illuminate\Support\Traits\Conditionable[]|mixed
     * @author 2024/6/19 14:23
     */
    public function listRoleOptions()
    {
        $query    = Role::query();
        $isRoot   = LoginService::getInstance()->isRoot();
        $query    = $query->when(!$isRoot, function ($query) {
            $query->where('code', '<>', Constant::ROOT_ROLE_CODE);
        });
        $roleList = $query
            ->orderBy('sort')
            ->get(['id', 'name']);

        $roleList = Helpers::model2Options($roleList->toArray());

        return $roleList;
    }


    public function saveRole(RoleFormInput $input)
    {
        $roleCode = $input->code;
        $roleId   = $input->id;
        if ($input->id) {
            // 编辑角色时，判断角色是否存在
            $role = Role::query()->where('id', $input->id)->first();
            if (is_null($role)) {
                //角色不存在
                $this->throwBusinessException();
            }
        } else {
            $role = Role::new();
        }

        $count = Role::query()
            ->when($roleId, function ($query) use ($roleId) {
                $query->where('id', '<>',$roleId);
            })
            ->where(function ($query) use ($input) {
                $query->where('code', $input->code)->orWhere('name', $input->name);
            })->count();

        if ($count > 0) {
            //角色名称或角色编码已存在，请修改后重试！
            $this->throwBusinessException();
        }

        $role->name       = $input->name;
        $role->code       = $input->code;
        $role->sort       = $input->sort;
        $role->status     = $input->status;
        $role->data_scope = $input->dataScope;
        //获取原始数据
        $oldRole = $role->getOriginal();
        $result           = $role->save();
        if ($result) {
            // 判断角色编码或状态是否修改，修改了则刷新权限缓存
            if ($oldRole && ($roleCode != $oldRole['code'] || $input->status != $oldRole['status'])) {
                RoleMenuService::getInstance()->refreshRolePermsCacheChange($oldRole['code'], $input->code);
            }
        }
        return $role;
    }


    /**
     * 获取角色表单数据
     *
     * @param $roleId
     * @return array
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/19 15:02
     */
    public function getRoleForm($roleId)
    {
        $column = ['id', 'name', 'sort','status', 'code', 'data_scope'];
        $form   = Role::query()->where('id', $roleId)->first($column);
        if (is_null($form)) {
            //角色不存在
            $this->throwBusinessException();
        }
        $form = $form->toArray();
        return $form;
    }

    /**
     * 批量删除角色
     *
     * @param $roleIds 角色ID，多个使用英文逗号(,)分割
     * @return bool
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/19 15:21
     */
    public function deleteRoles($roleIds)
    {
        if (!$roleIds) {
            //删除的角色ID不能为空
            $this->throwBusinessException();
        }
        $roleIds = explode(',', $roleIds);
        foreach ($roleIds as $roleId) {
            $role = Role::query()->where('id', $roleId)->first();
            if (is_null($role)){
                //角色不存在
                $this->throwBusinessException(CodeResponse::SYSTEM_EXECUTION_ERROR, '角色不存在');
            }
            $isRoleAssigned = UserRoleService::getInstance()->hasAssignedUsers($roleId);
            if ($isRoleAssigned) {
                $this->throwBusinessException(CodeResponse::SYSTEM_EXECUTION_ERROR, sprintf('角色【%s】已分配用户，请先解除关联后删除', $role->name));
            }
            $deleteResult = Role::query()->where('id', $roleId)->delete();
            if ($deleteResult) {
                // 删除成功，刷新权限缓存
                RoleMenuService::getInstance()->refreshRolePermsCache($role->code);
            }
        }
        return true;
    }


    /**
     * 修改角色状态
     *
     * @param $roleId 角色ID
     * @param $status 角色状态(1:启用；0:禁用)
     * @return bool
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/19 15:25
     */
    public function updateRoleStatus($roleId, $status)
    {
        $role = Role::query()->where('id', $roleId)->first();
        if (is_null($role)){
            //角色不存在
            $this->throwBusinessException();
        }
        $role->status = $status;
        $result = $role->save();
        if ($result) {
            // 状态修改成功，刷新权限缓存
            RoleMenuService::getInstance()->refreshRolePermsCache($role->code);
        }
        return true;
    }

}
