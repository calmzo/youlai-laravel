<?php


namespace App\Utils;


/**
 * Redis Key常量
 *
 * Class RedisConstant
 * @package App\Utils
 *
 */
class RedisConstant
{

    /**
     * 系统配置Redis-key
     */
    const SYSTEM_CONFIG_KEY = "system:config:";

    /**
     * IP限流Redis-key
     */
    const IP_RATE_LIMITER_KEY = "ip:rate:limiter:";

    /**
     * 黑名单列表Redis-key
     */
    const USER_BLACK_LIST = "user:black:list:";

    /**
     * 频繁请求列表Redis-key
     */
    const USER_FREQUENT_REQUEST_LIST = "user:frequent:request:list:";

    /**
     * 频繁请求锁Redis-key
     */
    const USER_FREQUENT_REQUEST_LOCK = "user:frequent:request:lock:";


    /**
     * 防重复提交Redis-key
     */
    const RESUBMIT_LOCK_PREFIX = "resubmit:lock:";

    /**
     * 单个IP请求的最大每秒查询数（QPS）阈值Key
     */
    const IP_QPS_THRESHOLD_LIMIT_KEY = "IP_QPS_THRESHOLD_LIMIT";

    /**
     * 手机验证码缓存前缀
     */

    const MOBILE_VERIFICATION_CODE_PREFIX = "VERIFICATION_CODE:MOBILE:";


    /**
     * 邮箱验证码缓存前缀
     */
    const EMAIL_VERIFICATION_CODE_PREFIX = "VERIFICATION_CODE:EMAIL:";


    /**
     * 客服列表key
     */
    const CUSTOMER_LIST_KEY = "customer:list:";


    /**
     * 客服列表缓存时间
     */
    const CUSTOMER_LIST_TTL = "3600";

    /**
     * 渠道列表key
     */
    const CHANNEL_LIST_KEY = "channel:list:";


    /**
     * 渠道列表缓存时间
     */
    const CHANNEL_LIST_TTL = "3600";

    /**
     * 客服分配规则Redis-key
     */
    const CUSTOMER_ASSIGN_RULE = "customer:assignRule:";

    /**
     * 客服分配上限Redis-key
     */
    const CUSTOMER_ASSIGN_LIMIT = "customer:assignLimit:";

    /**
     * 线索时间段线索数Redis-key
     */
    const THREAD_PERIOD_NUM_REDIS_KEY = "production_thread_period_num_merchant_id_";

}
