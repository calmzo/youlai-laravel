<?php

namespace App\Inputs\Admin\System;

use App\Inputs\PageInput;

class UserNoticePageInput extends PageInput
{
    public $name;
    public $group;


    public function rule()
    {
        return [
            'name'  => 'string',
            'group' => 'string',
        ];
    }

    public function message()
    {
        return [

        ];
    }

}
