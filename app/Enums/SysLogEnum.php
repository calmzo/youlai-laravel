<?php

namespace App\Enums;

class SysLogEnum extends BaseEnums
{

    const MODULE_SYSTEM_LOGIN = 'SYSTEM_LOGIN'; //系统登录
    const MODULE_SYSTEM_USER = 'SYSTEM_USER'; //系统用户
    const MODULE_SYSTEM_ROLE = 'SYSTEM_ROLE'; //系统角色
    const MODULE_SYSTEM_DEPT = 'SYSTEM_DEPT'; //系统部门
    const MODULE_SYSTEM_MENU = 'SYSTEM_MENU'; //系统菜单
    const MODULE_SYSTEM_DICT = 'SYSTEM_DICT'; //系统字典
    const MODULE_SYSTEM_CONFIG = 'SYSTEM_CONFIG'; //系统配置
    const MODULE_SYSTEM_NOTICE = 'SYSTEM_NOTICE'; //通知管理

    public static function moduleMap($key = null)
    {
        $list = [
            self::MODULE_SYSTEM_LOGIN  => '系统登录',
            self::MODULE_SYSTEM_USER   => '系统用户',
            self::MODULE_SYSTEM_ROLE   => '系统角色',
            self::MODULE_SYSTEM_DEPT   => '系统部门',
            self::MODULE_SYSTEM_MENU   => '系统菜单',
            self::MODULE_SYSTEM_DICT   => '系统字典',
            self::MODULE_SYSTEM_CONFIG => '系统配置',
            self::MODULE_SYSTEM_NOTICE => '通知管理',
        ];

        return self::getDesc($list, $key);
    }

}

