<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Admin\BaseController;
use App\Jobs\LoginNotice;
use App\Lib\MqProducer;
use App\Services\System\AuthService;
use App\Services\System\UserService;
use App\Utils\CodeResponse;
use App\Utils\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{
    protected $except = ['login', 'getCaptcha', 'refreshToken'];

    /**
     * @logAnnotation('系统登录','SYSTEM_LOGIN')
     *
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 11:56
     */
    public function login(Request $request)
    {
        $username    = $this->verifyString('username');
        $password    = $this->verifyString('password');
        $captchaKey  = $this->verifyString('captchaKey');
        $captchaCode = $this->verifyString('captchaCode');

        //图形验证码校验
        AuthService::getInstance()->checkLoginCaptcha($captchaKey, $captchaCode);

        if (empty($username) || empty($password)) {
            return $this->fail(CodeResponse::SYSTEM_EXECUTION_ERROR, '请输入用户名或密码');
        }
        $user = UserService::getInstance()->getByUsername($username);

        if (is_null($user)) {
            return $this->fail(CodeResponse::USERNAME_OR_PASSWORD_ERROR);
        }
        if ($user->status != Constant::OPEN) {
            return $this->fail(CodeResponse::USERNAME_OR_PASSWORD_ERROR, '账号已被禁用');
        }

        $isPass = Hash::check($password, $user->getAuthPassword());

        if (!$isPass) {
            return $this->fail(CodeResponse::USERNAME_OR_PASSWORD_ERROR, '账号和密码不正确');
        }
        $token = Auth::guard('admin')->login($user);
        $refreshToken = Auth::guard('admin')->setTTL(env('JWT_REFRESH_TTL'))->login($user);
        //日志

//        //mq方式一队列
//        $data = [
//            'uid'      => $user['id'],
//            'username' => $user['username'],
//            'message'  => '登陆成功获取了令牌',
//        ];
//        MqProducer::getInstance()->pushMessage($data, 'direct_queue');

        //mq方式二队列
//        LoginNotice::dispatch([
//            'name' => '这是一个测试队列',
//            'data' => [
//                'uid'      => $user['id'],
//                'username' => $user['username'],
//                'message'  => '登陆成功获取了令牌',
//            ]
//        ])->onQueue('test_mq_class');

        return $this->success([
            'accessToken'  => $token,
            'expires'      => env('JWT_TTL'),
            'refreshToken' => $refreshToken ?? '',
            'tokenType'    => 'Bearer',
        ]);
    }

    public function refreshToken()
    {
        $refreshToken = $this->verifyString('refreshToken');
        \request()->headers->set('Authorization', 'Bearer ' . $refreshToken);
        $expires = env('JWT_REFRESH_TTL');
        $newAccessToken = Auth::guard('admin')->setTTL($expires)->login($this->user());
        return $this->success([
            'accessToken'  => $newAccessToken,
            'expires'      => $expires,
            'refreshToken' => $refreshToken,
            'tokenType'    => 'Bearer',
        ]);
    }

    /**
     * 退出登录
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/6/20 11:56
     */
    public function logout()
    {
        Auth::guard('admin')->logout();
        return $this->success();
    }

    /**
     * 获取验证码
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/8/20 10:19
     */
    public function getCaptcha()
    {
        $mobile = $this->verifyId('mobile');
        $captcha = AuthService::getInstance()->getCaptcha($mobile);
        return $this->success($captcha);
    }
}
