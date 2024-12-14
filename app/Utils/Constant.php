<?php


namespace App\Utils;


class Constant
{
    const ERROR_STR = '-';

    /**
     * 验证码缓存前缀
     */
    const CAPTCHA_CODE_PREFIX = "captcha_code:";

    /**
     * 角色和权限缓存前缀
     */
    const ROLE_PERMS_PREFIX = "role_perms:";

    /**
     * 角色和权限缓存前缀
     */
    const MENU_ROUTES_PREFIX = "menu_routes:";


    /**
     * 黑名单Token缓存前缀
     */
    const BLACKLIST_TOKEN_PREFIX = "token:blacklist:";


    /**
     * 登录路径
     */
    const LOGIN_PATH = "/admin/auth/login";


    /**
     * JWT Token 前缀
     */
    const JWT_TOKEN_PREFIX = "Bearer ";


    /**
     * 根节点ID
     */
    const ROOT_NODE_ID = 0;


    /**
     * 系统默认密码
     */
    const DEFAULT_PASSWORD = "123456";

    /**
     * 超级管理员角色编码
     */
    const ROOT_ROLE_CODE = "ROOT";


    /**
     * 用户ID
     */
    const USER_ID = "userId";

    /**
     * 部门ID
     */
    const DEPT_ID = "deptId";

    /**
     * 数据权限
     */
    const DATA_SCOPE = "dataScope";

    /**
     * 权限(角色Code)集合
     */
    const AUTHORITIES = "authorities";

    /**
     * 商户金额前缀
     */
    const MERCHANT_AMOUNT_PREFIX_KEY = "production_merchant_amount_v2_";

    /**
     * 线索高峰期次数
     */
    const THREAD_PERIOD_NUM_REDIS_KEY = "production_merchant_amount_v2_";


    /**
     * 原画key
     */
    const YUANHUA_CUSTOMER_REDIS_KEY = "production_wm_yuanhua_merchant_customer_service_list_v2";


    /**
     * 商户key
     */
    const CUSTOMER_REDIS_KEY = "production_wm_merchant_customer_service_list_";


    /**
     * 商户今日零线索次数
     */
    const MERCHANT_TODAY_APP_ZERO_THREAD_NUM_REDIS_KEY = "production_merchant_today_app_zero_thread_num_";

    /**
     * 商户今日线索次数
     */
    const MERCHANT_TODAY_APP_THREAD_NUM_REDIS_KEY = "production_merchant_today_app_thread_num_";


    /**
     * 线索高峰时间
     */
    const THREAD_PEAKTIME_START = "08:00:00";
    const THREAD_PEAKTIME_END = "11:00:00";


    const OPEN = 1;
    const CLOSE = 0;

    //开启状态
    public static function openMap($key = null)
    {
        $list = [
            self::OPEN  => '开启',
            self::CLOSE => '关闭',
        ];
        return self::getDesc($list, $key);
    }

    public static function getDesc($list = [], $key = null)
    {
        return is_null($key) ? $list : ($list[$key] ?? self::ERROR_STR);
    }

}
