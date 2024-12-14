<?php

namespace App\Inputs\Admin\System;

use App\Inputs\Input;

class RoleFormInput extends Input
{
    public $id;
    public $name; //角色名称
    public $code;//角色编码
    public $sort; //排序
    public $status; //角色状态(1-正常；0-停用)
    public $dataScope; //数据权限

    public function rule()
    {
        return [
            'name'                   => 'required |string',
            'code'                   => 'required |string',

        ];
    }

    public function message()
    {
        return [

        ];
    }

}
