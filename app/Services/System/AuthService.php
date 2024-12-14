<?php

namespace App\Services\System;

use App\Exceptions\BusinessException;
use App\Services\BaseService;
use App\Tools\Helpers;
use App\Utils\CaptchaProperties;
use App\Utils\CodeResponse;
use App\Utils\Constant;
use App\Utils\RedisConstant;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Support\Facades\Cache;
use App\Notifications\LoginSmsNotify;


class AuthService extends BaseService
{
    /**
     * 发送验证码
     *
     * @param null $mobile
     * @return array|bool
     * @throws BusinessException
     * @author 2024/8/20 10:18
     */
    public function getCaptcha($mobile = null)
    {
        if (CaptchaProperties::TYPE == 1) {
            //1=图片验证码
            $width         = CaptchaProperties::WIDTH;
            $height        = CaptchaProperties::HEIGHT;
            $quality       = CaptchaProperties::QUALITY;
            $expireSeconds = CaptchaProperties::EXPIRE_SECONDS;
            // 将构建 5 个字符的短语，仅数字
            $phraseBuilder = new  PhraseBuilder(4, '0123456789');
            // 将其作为 CaptchaBuilder 的第一个参数传递，将短语
            $builder = new CaptchaBuilder(null, $phraseBuilder);
            $builder->build($width, $height);
            $captcha = $builder->inline($quality);  //输出base64格式图片
            $code    = $builder->getPhrase();  //验证码

            $captchaKey = Helpers::generateRandomString();
            Cache::add(Constant::CAPTCHA_CODE_PREFIX . $captchaKey, $code, $expireSeconds);
            return ['captchaBase64' => $captcha, 'captchaKey' => $captchaKey];
        } else {
            //短信验证码发送
            return $this->loginCaptcha($mobile);
        }
    }

    /**
     * 登录验证码
     *
     * @param $mobile
     * @return bool
     * @throws BusinessException
     * @author 2024/8/20 10:17
     */
    public function loginCaptcha($mobile)
    {
        $user = UserService::getInstance()->getByMobile($mobile);
        if (is_null($user)) {
            //用户不存在
            $this->throwBusinessException(CodeResponse::RESOURCE_NOT_FOUND, '用户不存在');
        }
        $expireSeconds = CaptchaProperties::EXPIRE_SECONDS;
        $lock = Cache::add('login_captcha_lock_' . $mobile, 1, $expireSeconds);
        if (!$lock) {
            $this->throwBusinessException();
        }
        $this->sendVerificationCode($mobile);
        return true;
    }

    /**
     * 发送短信验证码
     *
     * @param string $mobile
     * @return bool
     * @throws \Exception
     * @author 2024/8/20 10:16
     */
    public function sendVerificationCode(string $mobile)
    {
        // 随机生成4位验证码 测试环境123456
        $code = app()->env == 'prod' ? random_int(100000, 999999) : '123456';
        $verificationCodePrefix = RedisConstant::MOBILE_VERIFICATION_CODE_PREFIX;
        $user = UserService::getInstance()->getByMobile($mobile);
        $user->notify(new LoginSmsNotify($code, 'SMS_274645226'));
        // 存入 redis 用于校验, 5分钟有效
        Cache::put($verificationCodePrefix . $mobile, $code, 600);
        return true;
    }


    public function checkLoginCaptcha($captchaKey, $captchaCode)
    {

        $switch = $this->chenckLoginCaptchaSwitch();
        if (!$switch) {
            //默认打开验证，关闭则退出 return true;
            return true;
        }
        $key    = Constant::CAPTCHA_CODE_PREFIX . $captchaKey;
        $isPass = $captchaCode == Cache::get($key);
        if ($isPass) {
            Cache::forget($key);
            return true;
        } else {
            throw new BusinessException(CodeResponse::VERIFY_CODE_ERROR);
        }
    }


    public function chenckLoginCaptchaSwitch()
    {
        if (env('APP_ENV') != 'prod') {
            return false;
        }
        return true;
    }


}

