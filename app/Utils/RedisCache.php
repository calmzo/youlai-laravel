<?php

namespace App\Utils;

use App\Services\BaseService;
use Illuminate\Support\Facades\Redis;

class RedisCache extends BaseService
{


    public function exists($key)
    {
        return Redis::exists($key);
    }
    public function hMSet($key, $hashKeys)
    {
        return Redis::hmset($key, $hashKeys);
    }


    public function expireAt($key, $timestamp)
    {
        return Redis::expireAt($key, $timestamp);
    }

    public static function hIncrBy($hash, $key, $value = 1)
    {
        return Redis::hIncrBy($hash, $key, $value);
    }

    public function hMGet($key, array $hashKeys)
    {
        $hMGetList = Redis::hMGet($key, $hashKeys);
        $hMGetList = array_map(function ($item) {
            return is_array($item) ? $item : json_decode($item, true);
        }, $hMGetList);
        return $hMGetList;
    }
    public function hset($key, $hashKey, $value)
    {
        $value = is_array($value) ? json_encode($value) : $value;
        return Redis::hset($key, $hashKey, $value);
    }


    /**
     *
     * @param $hash
     * @param $key
     */
    public static function hGet($hash, $key)
    {
        $value = Redis::hGet($hash, $key);
        if (!$value) {
            return null;
        }

        $value = json_decode($value, true) ?: $value;
        return $value;
    }

    public function set($key, $value, $timeout = null)
    {
        $time = $timeout ?: config('redis.expire_time');
        return Redis::setex($key, $time, json_encode($value));
    }

    public function get($key)
    {
        $value = Redis::get($key);
        if (!$value) {
            return false;
        }
        return json_decode($value, true);
    }


    public function hdel($key, $hashKey)
    {
        return Redis::hdel($key, $hashKey);
    }

    public function delete($key)
    {
        return Redis::del($key);
    }

    public function keys($key)
    {
        return Redis::keys($key);
    }

    /**
     * 删除集合对象
     * @param $keys
     * @return mixed
     */
    public function deleteObject($keys = '*')
    {
        $keys = self::keys($keys);
        if ($keys) {
            foreach ($keys as $key) {
                Redis::del($key);
            }
        }
    }


    public function incrBy($key, $value = 1)
    {
        if ($value == 1) {
            return Redis::incr($key);
        }
        return Redis::incrBy($key, $value);
    }

    public function setEx($key, $ttl, $value)
    {
        return Redis::setEx($key, $ttl, $value);
    }

    public function decrBy($key, $value)
    {
        return Redis::decrBy($key, $value);
    }


    public function zScore($key, $member)
    {
        return Redis::zscore($key, $member);
    }

    public function zAdd($key, $options, $score1)
    {
        return Redis::zAdd($key, $options, $score1);
    }

    public function expire($key, $ttl = 30)
    {
        return Redis::expire($key, $ttl);
    }

}
