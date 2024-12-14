<?php

namespace App\Inputs\Admin\System;

use App\Inputs\Input;

class PasswordChangeFormInput extends Input
{

    public $oldPassword; //旧密码
    public $newPassword; //新密码
    public $confirmPassword; //确认密码

    public function rule()
    {
        return [
            'oldPassword'                   => 'required |string',
            'newPassword'                   => 'required |string',
            'confirmPassword'                   => 'required |string',
        ];
    }

    public function message()
    {
        return [

        ];
    }

}
