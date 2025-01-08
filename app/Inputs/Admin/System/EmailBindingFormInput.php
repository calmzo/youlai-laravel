<?php

namespace App\Inputs\Admin\System;

use App\Inputs\Input;

class EmailBindingFormInput extends Input
{
    public $email; //邮箱
    public $code; //验证码

    public function rule()
    {
        return [
            'email' => 'string',
            'code'  => 'string',
        ];
    }

    public function message()
    {
        return [

        ];
    }

}
