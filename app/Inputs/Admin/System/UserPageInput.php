<?php

namespace App\Inputs\Admin\System;

use App\Inputs\PageInput;

class UserPageInput extends PageInput
{
    public $keywords;
    public $deptId;
    public $roleId;
    public $status;
    public $createTime;


    public function rule()
    {
        return [
            'keywords'   => 'string',
            'deptId'    => 'integer',
            'roleId'     => 'integer',
            'createTime' => 'array',
        ];
    }

    public function message()
    {
        return [

        ];
    }

}
