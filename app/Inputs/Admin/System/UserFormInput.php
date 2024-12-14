<?php

namespace App\Inputs\Admin\System;

use App\Inputs\Input;

class UserFormInput extends Input
{
    public $id;
    public $username; //用户名
    public $nickname; //昵称
    public $mobile; //手机号码
    public $gender = 1; //性别
    public $avatar; //用户头像
    public $email; //邮箱
    public $status; //用户状态(1-正常；0-停用)
    public $deptId; //部门ID
    public $roleIds; //角色ID集合

    public function rule()
    {
        return [
            'username'                   => 'required |string',
            'nickname'                   => 'required |string',
            'roleIds'                   => 'required |array',

        ];
    }

    public function message()
    {
        return [

        ];
    }

}
