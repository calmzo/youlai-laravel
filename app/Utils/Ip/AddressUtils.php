<?php

namespace App\Utils\Ip;

use App\Utils\Http\HttpUtils;
use Illuminate\Support\Facades\Log;
use Torann\GeoIP\Facades\GeoIP;

class AddressUtils
{

    const IP_URL = "http://whois.pconline.com.cn/ipJson.jsp";
    const UNKNOWN = "未知";

    /**
     * 根据ip获取地址
     * @param $ip
     * @return string|void
     */
    public static function getRealAddressByIP($ip)
    {
        $province = '';
        $city     = '';
        $address  = '';
        if (IpUtils::internalIp($ip)) {
            $address = '内网IP';
        } else {
            $location = GeoIP::getLocation($ip);
            $province = $location->state_name ?? '';
            $city = $location->city ?? '';
            $address  = $province . ' ' . $city;
        }
        $info = [
            'province' => $province,
            'city'     => $city,
            'address'  => $address,
        ];
        return $info;
    }

}
