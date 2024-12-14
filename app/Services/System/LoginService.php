<?php

namespace App\Services\System;

use App\Models\System\SysUser as User;
use App\Services\BaseService;
use App\Utils\Constant;
use Illuminate\Support\Facades\Auth;

class LoginService extends BaseService
{
    public function user(): array
    {
        return Auth::guard('admin')->user() ? Auth::guard('admin')->user()->toArray() : [];
    }

    public function payload(): array
    {
        return Auth::guard('admin')->getPayload() ? Auth::guard('admin')->getPayload()->toArray() : [];
    }


    public function isLogin()
    {
        return !is_null(self::user());
    }

    /**
     * @return mixed
     */
    public function userId()
    {
        return Auth::guard('admin')->user()->getAuthIdentifier();
    }

    public function userName()
    {
        $user = $this->user();
        return $user['username'] ?? '';
    }

    /**
     * 获取用户角色集合
     *
     * @return array
     * @author 2024/6/20 18:53
     */
    public function getRoles()
    {
        $uid   = $this->userId();
        $user  = User::query()->with('roles')->where('id', $uid)->first(['id', 'username', 'nickname', 'avatar']);
        $roles = $user->roles->pluck('code')->toArray();
        return $roles;
    }

    /**
     * 是否超级管理员
     * 超级管理员忽视任何权限判断
     * @return bool
     * @author 2024/6/20 18:53
     */
    public function isRoot()
    {
        $roles = $this->getRoles();
        return in_array(Constant::ROOT_ROLE_CODE, $roles);
    }

}
