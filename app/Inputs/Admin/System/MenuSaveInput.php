<?php

namespace App\Inputs\Admin\System;

use App\Inputs\Input;

class MenuSaveInput extends Input
{
    public $id;
    public $parentId;
    public $name; //菜单名称
    public $type;//菜单类型(1-菜单；2-目录；3-外链；4-按钮权限)
    public $routePath; //路由路径
    public $routeName; //路由名称
    public $component; //组件路径(vue页面完整路径，省略.vue后缀)
    public $perm; //权限标识
    public $visible; //显示状态(1:显示;0:隐藏)
    public $sort; //排序(数字越小排名越靠前)
    public $icon; //菜单图标
    public $redirect; //跳转路径
    public $keepAlive; //【菜单】是否开启页面缓存 1
    public $alwaysShow; //【目录】只有一个子路由是否始终显示 1
    public $params; //路由参数


    public function rule()
    {
        return [
        ];
    }

    public function message()
    {
        return [

        ];
    }

}
