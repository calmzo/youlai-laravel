<?php
/**
 * BaseEnums
 */

namespace App\Enums;

use BenSampo\Enum\Enum;

class BaseEnums extends Enum
{

    const ERROR_STR = '';

    public static function getDesc($list = [], $key = null)
    {
        return is_null($key) ? $list : ($list[$key] ?? self::ERROR_STR);
    }

}
