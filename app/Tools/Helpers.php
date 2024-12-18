<?php


namespace App\Tools;

use App\Models\SystemConfig;
use App\Utils\Constant;
use Illuminate\Support\Facades\Cache;
use Faker\Factory;

class Helpers
{
    public static function generateNumber(string $prefix = 'JOB', int $length = 18)
    {
        $uid = uniqid($prefix, true);
        $uid = str_replace('.', '', $uid);
        $num = substr($prefix . $uid, 0, $length);
        for ($i = 0; $i < strlen($num); $i++) {
            if ($i >= strlen($prefix)) {
                $num[$i] = strtolower($num[$i]); //字母转换成小写
                //如果存在a-z的小写字母 转换成数字
                if (ord($num[$i]) >= 97 && ord($num[$i]) <= 122) {
                    $num[$i] = (ord($num[$i]) - 97) % 10;
                }
            }
        }
        return $num;
    }

    public static function hiddenPhoneNumber($tel)
    {
        if (empty($tel)) {
            return '';
        }
        return substr_replace($tel, str_repeat("*", 4), 3, 4);
    }

    //字段时间转换
    public static function timeToDate($time)
    {
        if (!$time || !is_int($time)) {
            return '-';
        }
        return date('Y-m-d H:i:s', $time);
    }

    public static function arrayToOptions($arr)
    {
        $newArr = $tmp = [];
        foreach ($arr as $k => $item) {
            $tmp['label'] = $item;
            $tmp['value'] = $k;
            $newArr[]     = $tmp;
        }
        return $newArr;
    }

    /**
     * 下拉转换
     *
     * @param $model
     * @param string $key
     * @param string $value
     * @return array
     * @author 2024/7/26 10:27
     */
    public static function model2Options($model, $key = 'id', $value = 'name')
    {
        $newArr = [];
        foreach ($model as $item) {
            $newArr[] = [
                'label' => $item[$value],
                'value' => $item[$key],
            ];
        }
        return $newArr;
    }


    /**
     * 生成随机字符串
     *
     * @param int $length
     * @return string
     * @author 2024/6/22 14:22
     */
    public static function generateRandomString($length = 10)
    {
        $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString     = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    /**
     * 时间范围匹配
     *
     * @return bool
     * @author 2024/7/12 15:06
     */
    public static function checkThreadRangeTime()
    {
        $startTime = Constant::THREAD_PEAKTIME_START;
        $endTime   = Constant::THREAD_PEAKTIME_END;
        $day       = date('Y-m-d ', time());
        $startTime = strtotime($day . $startTime);
        $endTime   = strtotime($day . $endTime);
        if (time() >= $startTime && time() <= $endTime) {
            return true;
        }
        return false;
    }

    /**
     * 金额元转成分
     */
    public static function floatToInt($var)
    {
        if ($var) {
            $var = floatval($var);
            $var = $var * 100;
            return (int)$var;
        }
        return 0;
    }


    public static function sysConfig($group, $name = null)
    {
        $value = $name ? Cache::get("sysConfig_{$group}_{$name}") : Cache::get("sysConfig_{$group}");
        if (empty($value)) {
            if ($name) {
                $value = SystemConfig::where('group', $group)->where('name', $name)->first();
                Cache::set("sysConfig_{$group}_{$name}", $value, 3600);
            } else {
                $value = SystemConfig::where('group', $group)->pluck('value', 'name');
                Cache::set("sysConfig_{$group}", $value, 3600);
            }
        }
        return $value;
    }


    /**
     * 手机号脱敏
     *
     * @param $phone
     * @return string
     * @author 2024/8/22 19:07
     */
    public static function desensitizePhone($phone) {
        if (!$phone) {
            return '';
        }
        return substr($phone, 0, 3) . '****' . substr($phone, 7);
    }

    /**
     * 生成随机手机号
     *
     * @return string
     * @author 2024/8/29 09:52
     */
    public static function randomPhoneNumber() {
        $faker = Factory::create('zh_CN');
        return $faker->phoneNumber;
    }

    /**
     * 生成随机城市
     *
     * @return array
     * @author 2024/8/29 09:58
     */
    public static function randomProvinceCity()
    {
        $faker = Factory::create();
        return ['province' => $faker->city, 'city' => $faker->city];
    }

    public static function curlPost($url, $body, $headers = [])
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);//设置请求体1
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');//使用一个自定义的请求信息来代替"GET"或"HEAD"作为HTTP请求。(这个加不加没啥影响)
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        $data = curl_exec($curl);
        if ($data === false) {
            return false;
        } else {
            return $data;
        }
    }


    public static function periodDate($startDate, $endDate)
    {
        $startTime = strtotime($startDate);
        $endTime   = strtotime($endDate);
        $arr       = [];
        while ($startTime <= $endTime) {
            $arr[]     = date('Y-m-d', $startTime);
            $startTime = strtotime('+1 day', $startTime);
        }
        return $arr;
    }
}
