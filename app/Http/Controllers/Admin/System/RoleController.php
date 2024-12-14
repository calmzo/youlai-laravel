<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Admin\BaseController;
use App\Inputs\Admin\System\RoleFormInput;
use App\Inputs\Admin\System\RolePageInput;
use App\Services\System\RoleService;
use Illuminate\Http\Request;

class RoleController extends BaseController
{
    public $except = [];

    /**
     * 获取角色的菜单ID集合
     *
     * @param $roleId
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/6/11 11:07
     */
    public function getRoleMenuIds($roleId)
    {
        $menus = RoleService::getInstance()->getRoleMenuIds($roleId);
        return $this->success($menus);
    }

    /**
     * 分配菜单(包括按钮权限)给角色
     * @permission('hasPermi','sys:role:assign')
     * @logAnnotation('分配系统菜单权限','SYSTEM_ROLE')
     * @param $roleId
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/6/19 15:26
     */
    public function assignMenusToRole($roleId, Request $request)
    {
        $menuIds = $request->input();
        $menus = RoleService::getInstance()->assignMenusToRole($roleId, $menuIds);
        return $this->success($menus);
    }

    /**
     * 角色分页列表
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/19 14:18
     */
    public function getRolePage()
    {
        $input = RolePageInput::new();
        $paginate = RoleService::getInstance()->getRolePage($input);
        return $this->successPaginate($paginate);
    }

    /**
     * 角色下拉列表
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/6/19 14:24
     */
    public function listRoleOptions()
    {
        $list = RoleService::getInstance()->listRoleOptions();
        return $this->success($list);
    }


    /**
     * 新增角色
     * @permission('hasPermi','sys:role:add')
     * @logAnnotation('新增系统角色','SYSTEM_ROLE')
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 12:01
     */
    public function addRole()
    {
        $input = RoleFormInput::new();
        $role = RoleService::getInstance()->saveRole($input);
        return $this->success(true);
    }

    /**
     * 修改角色
     * @permission('hasPermi','sys:role:edit')
     * @logAnnotation('修改系统角色','SYSTEM_ROLE')
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 12:02
     */
    public function updateRole($id)
    {
        $input = RoleFormInput::new();
        $role = RoleService::getInstance()->saveRole($input);
        return $this->success(true);
    }


    /**
     * 获取角色表单数据
     * @param $roleId
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/19 15:02
     */
    public function getRoleForm($roleId)
    {
        $list = RoleService::getInstance()->getRoleForm($roleId);
        return $this->success($list);

    }


    /**
     * 删除角色
     * @permission('hasPermi','sys:role:delete')
     * @logAnnotation('删除系统角色','SYSTEM_ROLE')
     * @param $roleIds
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/19 15:21
     */
    public function deleteRoles($roleIds)
    {
        $res = RoleService::getInstance()->deleteRoles($roleIds);
        return $this->success($res);
    }


    /**
     * 修改角色状态
     * @logAnnotation('修改系统角色状态','SYSTEM_ROLE')
     * @param $roleId
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/19 15:26
     */
    public function updateRoleStatus($roleId)
    {
        $status = $this->verifyBoolean('status');
        $list = RoleService::getInstance()->updateRoleStatus($roleId, $status);
        return $this->success($list);

    }
}
