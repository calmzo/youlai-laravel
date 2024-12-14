<?php

namespace App\Enums;

class MenuEnum extends BaseEnums
{
    //类型
    const TYPE_MENU = 1; //菜单
    const TYPE_CATALOG = 2; //目录
    const TYPE_EXTLINK = 3; //外链
    const TYPE_BUTTON = 4; //按钮

    /**
     * @param null $key
     * @return array|mixed|string
     */
    public static function typeStrMap($key = null)
    {
        $list = [
            self::TYPE_MENU    => '菜单',
            self::TYPE_CATALOG => '目录',
            self::TYPE_EXTLINK => '外链',
            self::TYPE_BUTTON  => '按钮',
        ];
        return self::getDesc($list, $key);
    }



    //类型
    const TYPE_MENU_STR = 'MENU'; //菜单
    const TYPE_CATALOG_STR = 'CATALOG'; //目录
    const TYPE_EXTLINK_STR = 'EXTLINK'; //外链
    const TYPE_BUTTON_STR = 'BUTTON'; //按钮

    /**
     * @param null $key
     * @return array|mixed|string
     */
    public static function typeMap($key = null)
    {
        $list = [
            self::TYPE_MENU_STR    => '1',
            self::TYPE_CATALOG_STR => '2',
            self::TYPE_EXTLINK_STR => '3',
            self::TYPE_BUTTON_STR  => '4',
        ];
        return self::getDesc($list, $key);
    }



    //类型
    const TYPE_MENU_DESC = 1; //菜单
    const TYPE_CATALOG_DESC = 2; //目录
    const TYPE_EXTLINK_DESC = 3; //外链
    const TYPE_BUTTON_DESC = 4; //按钮

    /**
     * @param null $key
     * @return array|mixed|string
     */
    public static function typeDescMap($key = null)
    {
        $list = [
            self::TYPE_MENU    => 'MENU',
            self::TYPE_CATALOG => 'CATALOG',
            self::TYPE_EXTLINK => 'EXTLINK',
            self::TYPE_BUTTON  => 'BUTTON',
        ];
        return self::getDesc($list, $key);
    }


    /**
     * 任务状态
     */

    const TASK_STATUS_CREATE = 101;
    const TASK_STATUS_AUTO_CANCEL = 102;
    const TASK_STATUS_ADMIN_CANCEL = 103;
    const TASK_STATUS_CANCEL = 104;
    const TASK_STATUS_CANCEL_UNQUALIFIED = 105; //不合格
    const TASK_STATUS_AUDIT_WAIT = 106; //待审核
    const TASK_STATUS_PAY_FINISH = 201;

    public static function statusMap($key = null)
    {
        $list = [
            self::TASK_STATUS_CREATE             => '待完成',
            self::TASK_STATUS_AUTO_CANCEL        => '已取消',
            self::TASK_STATUS_ADMIN_CANCEL       => '已取消',
            self::TASK_STATUS_CANCEL             => '已取消',
            self::TASK_STATUS_CANCEL_UNQUALIFIED => '不合格',
            self::TASK_STATUS_PAY_FINISH         => '已完成',

        ];
        return self::getDesc($list, $key);
    }

    //后台下拉
    public static function statusShowMap($key = null)
    {
        $list = [
            self::TASK_STATUS_CREATE             => '待完成',
            self::TASK_STATUS_ADMIN_CANCEL       => '已取消',
            self::TASK_STATUS_CANCEL_UNQUALIFIED => '不合格',
            self::TASK_STATUS_PAY_FINISH         => '已完成',

        ];
        return self::getDesc($list, $key);
    }


    const TASK_LIVE_STATUS_NO = 1;
    const TASK_LIVE_STATUS_ING = 2;
    const TASK_LIVE_STATUS_FINISH = 3;

    public static function liveStatusMap($key = null)
    {
        $list = [
            self::TASK_LIVE_STATUS_NO     => '未开始',
            self::TASK_LIVE_STATUS_ING    => '直播中',
            self::TASK_LIVE_STATUS_FINISH => '已结束',

        ];
        return self::getDesc($list, $key);
    }
}

