<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Admin\BaseController;
use App\Inputs\Admin\System\MenuListInput;
use App\Inputs\Admin\System\MenuSaveInput;
use App\Services\System\LoginService;
use App\Services\System\MenuService;

class MenuController extends BaseController
{
    public $except = [];

    /**
     * 获取菜单下拉列表
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/6/20 11:58
     */
    public function listMenuOptions()
    {
        $menus = MenuService::getInstance()->listMenuOptions();
        return $this->success($menus);
    }


    /**
     * 获取菜单列表
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 11:58
     */
    public function listMenus()
    {
        $input = MenuListInput::new();
        $menus = MenuService::getInstance()->listMenus($input);
        return $this->success($menus);
    }


    /**
     * 菜单路由列表
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/6/20 11:59
     */
    public function listRoutes()
    {
        $roles = LoginService::getInstance()->getRoles();
        $menus = MenuService::getInstance()->listRoutes($roles);
        return $this->success($menus);
    }


    /**
     * 获取菜单表单数据
     * @param $deptId
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/6/18 13:34
     */
    public function getMenuForm($deptId)
    {
        $list = MenuService::getInstance()->getMenuForm($deptId);
        return $this->success($list);

    }


    /**
     * 新增菜单
     * @permission('hasPermi','sys:menu:add')
     * @logAnnotation('新增系统菜单','SYSTEM_MENU')
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 11:59
     */
    public function saveMenu()
    {
        $input = MenuSaveInput::new();
        $menu = MenuService::getInstance()->saveMenu($input);
        return $this->success(true);
    }

    /**
     * 修改菜单
     * @permission('hasPermi','sys:menu:edit')
     * @logAnnotation('修改修通菜单','SYSTEM_MENU')
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 11:59
     */
    public function updateMenu($id)
    {
        $input = MenuSaveInput::new();
        $menu = MenuService::getInstance()->saveMenu($input);
        return $this->success(true);
    }

    /**
     * 删除菜单
     * @permission('hasPermi','sys:menu:delete')
     * @logAnnotation('删除系统菜单','SYSTEM_MENU')
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \Exception
     * @author 2024/6/20 12:00
     */
    public function deleteMenu($id)
    {
        $res = MenuService::getInstance()->deleteMenu($id);
        return $this->success($res);
    }

    /**
     * 修改菜单显示状态
     * @logAnnotation('修改系统菜单状态','SYSTEM_MENU')
     * @param $menuId
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 12:00
     */
    public function updateMenuVisible($menuId)
    {
        $visible = $this->verifyBoolean('visible');
        $res = MenuService::getInstance()->updateMenuVisible($menuId, $visible);
        return $this->success($res);
    }
}
