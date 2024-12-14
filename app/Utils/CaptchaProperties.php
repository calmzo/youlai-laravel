<?php


namespace App\Utils;


class CaptchaProperties
{

    /**
     * 验证码类型 1=图片验证码 2=手机验证码
     */
    const TYPE = 1;

    /**
     * 验证码图片宽度
     */
    const WIDTH = 120;
    /**
     * 验证码图片高度
     */
    const HEIGHT = 40;

    /**
     * 质量
     */
    const QUALITY = 90;


    /**
     * 验证码过期时间，单位：秒
     */
    const EXPIRE_SECONDS = 300;

}
