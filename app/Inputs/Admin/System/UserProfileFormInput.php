<?php

namespace App\Inputs\Admin\System;

use App\Inputs\Input;

class UserProfileFormInput extends Input
{
    public $id;
    public $username; //用户名
    public $nickname; //昵称
    public $avatar; //用户头像
    public $gender; //性别
    public $mobile; //手机号码
    public $email; //邮箱

    public function rule()
    {
        return [
            'username'                   => 'string',
            'nickname'                   => 'string',
            'avatar'                   => 'string',
            'gender'                   => 'integer',
            'mobile'                   => 'string',
            'email'                   => 'string',

        ];
    }

    public function message()
    {
        return [

        ];
    }

}
