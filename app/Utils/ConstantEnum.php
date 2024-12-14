<?php

namespace App\Utils;

use App\Enums\BaseEnums;

class ConstantEnum extends BaseEnums
{
    const ERROR_STR = '-';

    const GENDER_MALE = 1; // 男
    const GENDER_FEMALE = 2; // 女

    /**
     * @param null $key
     * @return array|mixed|string
     */
    public static function genderMap($key = null)
    {
        $list = [
            self::GENDER_MALE   => '男',
            self::GENDER_FEMALE => '女',
        ];

        return self::getDesc($list, $key);
    }


    const STATUS_OPEN = 1; // 开启
    const STATUS_CLOSE = 2; // 关闭
    public static function statusMap($key = null)
    {
        $list = [
            self::STATUS_OPEN  => '开启',
            self::STATUS_CLOSE => '关闭',
        ];

        return self::getDesc($list, $key);
    }

    const CASH_ZFB=1;//支付宝
    const CASH_XH=2;//银行卡
    const CASH_WX=3;//微信
    public static function cashStatus($key=null)
    {
        $list = [
            self::CASH_ZFB  => '支付宝',
            self::CASH_XH => '银行卡',
            self::CASH_WX => '微信',
        ];

        return self::getDesc($list, $key);
    }

    const PAY_SUCCESS=1;//打款成功
    const REFUSAL=2;//驳回打款
    public static function makePaymentStatus($key=null)
    {
        $list = [
            self::PAY_SUCCESS  => '打款成功',
            self::REFUSAL => '驳回打款',
        ];

        return self::getDesc($list, $key);
    }
}
