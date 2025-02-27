<?php

namespace App\Http\Middleware;

use App\Exceptions\Token\ForbiddenException;
use App\Services\Lib\RobotService;
use App\Services\System\ConfigService;
use App\Utils\CodeResponse;
use App\Utils\RedisCache;
use App\Utils\RedisConstant;
use Illuminate\Support\Facades\Log;

class RateLimit
{
    /**
     * 执行中间件
     *
     * @param $request
     * @param \Closure $next
     * @return mixed
     * @throws ForbiddenException
     * @author 2024/8/20 15:03
     */
    public function handle($request, \Closure $next)
    {
        $ip           = $request->server('HTTP_X_FORWARDED_FOR') ?? $request->ip();
        $checkBlackIp = $this->checkBlackIp($ip);
        if ($checkBlackIp) {
            Log::channel('rateLimit')->error(sprintf('黑名单，已限制访问：ip:【%s】', $ip));
            throw new ForbiddenException(CodeResponse::USER_ERROR, '黑名单，已限制访问');
        }
        $checkBlackIp = $this->checkRepeatIp($ip);
        if ($checkBlackIp) {
            Log::channel('rateLimit')->error(sprintf('频繁请求，已加黑名单：ip:【%s】', $ip));
            throw new ForbiddenException(CodeResponse::USER_ERROR, '频繁请求，已加黑名单');
        }
        return $next($request);
    }


    /**
     * 校验黑名单ip
     *
     * @param $ip
     * @return bool
     * @author 2024/8/20 14:45
     */
    public function checkBlackIp($ip)
    {
        $isBlack = RedisCache::getInstance()->zscore(RedisConstant::USER_BLACK_LIST, $ip);
        if ($isBlack) {
            return true;
        }
        return false;
    }


    /**
     *
     * 判断 IP 是否触发限流
     * 默认限制同一 IP 每秒最多请求 10 次，可通过系统配置调整。
     * 如果系统未配置限流阈值，默认跳过限流。
     *
     * @param $ip IP 地址
     * @return bool 是否限流：true 表示限流；false 表示未限流
     * @author 2024/8/20 15:00
     */
    public function checkRepeatIp($ip)
    {
        //单个IP请求的最大每秒查询数（QPS）阈值Key
        $ip_qps_threshold_limit_key = RedisConstant::IP_QPS_THRESHOLD_LIMIT_KEY;
        //黑名单列表Redis-key
        $user_black_list_key = RedisConstant::USER_BLACK_LIST;
        //频繁请求锁Redis-key
        $user_frequent_request_lock_key = RedisConstant::USER_FREQUENT_REQUEST_LOCK;

        $redis       = RedisCache::getInstance();
        $time        = 1;//锁过期时间 每秒QPS
        $limit       = 20;//QPS，超过加入黑名单
        $limitConfig = ConfigService::getInstance()->getSystemConfig($ip_qps_threshold_limit_key);
        if (!$limitConfig) {
            //系统未配置限流阈值，跳过限流
            return false;
        }
        $limit       = $limitConfig;

        $frequentLock = $user_frequent_request_lock_key . $ip;
        $isFrequent   = $redis->get($frequentLock);
        if (!$isFrequent) {
            $redis->setEx($frequentLock, $time, 1); //设置锁
        }
        //设定时间内请求次数大于设定次数，加入黑名单
        if ($isFrequent > $limit) {
            $redis->zAdd($user_black_list_key, 1, $ip);
            RobotService::getInstance()->smsErrorNotice(['msg' => sprintf('频繁请求，操作加入黑名单：ip:【%s】', $ip)]);
            return true;
        }
        $redis->incrBy($frequentLock);//锁过期时间类数值自增
        return false;
    }
}
