<?php

namespace App\Enums;

class SysNoticeEnum extends BaseEnums
{

    const TARGET_TYPE_ALL = 1; //全体
    const TARGET_TYPE_SPECIFIED = 2; //指定

    public static function targetTypeMap($key = null)
    {
        $list = [
            self::TARGET_TYPE_ALL  => '全体',
            self::TARGET_TYPE_SPECIFIED   => '指定',
        ];

        return self::getDesc($list, $key);
    }

    const PUBLISH_STATUS_UNPUBLISHED = 0; //未发布
    const PUBLISH_STATUS_PUBLISHED = 1; //已发布
    const PUBLISH_STATUS_REVOKED = -1; //已撤回

    public static function publishStatusMap($key = null)
    {
        $list = [
            self::PUBLISH_STATUS_UNPUBLISHED  => '全体',
            self::PUBLISH_STATUS_PUBLISHED   => '指定',
            self::PUBLISH_STATUS_REVOKED   => '指定',
        ];

        return self::getDesc($list, $key);
    }


}

