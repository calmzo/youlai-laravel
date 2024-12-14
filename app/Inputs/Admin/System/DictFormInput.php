<?php

namespace App\Inputs\Admin\System;

use App\Inputs\Input;

class DictFormInput extends Input
{
    public $id; //字典ID
    public $dictCode; //类型编码
    public $name; //字典名称
    public $status; //状态(1-正常；0-禁用)
    public $remark; //备注

    public function rule()
    {
        return [
            'id'        => 'integer',
            'dictCode'  => 'required|string|max:50',
            'name'      => 'required|string|max:50',
            'status'    => 'required|integer|in:0,1',
        ];
    }

    public function message()
    {
        return [

        ];
    }

}
