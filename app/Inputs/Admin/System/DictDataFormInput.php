<?php

namespace App\Inputs\Admin\System;

use App\Inputs\Input;

class DictDataFormInput extends Input
{
    public $id; //字典ID
    public $dictCode; //编码
    public $label;
    public $value;
    public $sort;
    public $status;
    public $tagType;

    public function rule()
    {
        return [
            'id'       => 'integer',
            'dictCode' => 'required|string|max:50',
            'label'    => 'required|string|max:50',
            'value'    => 'required|string|max:50',
            'tagType'  => 'required|string|max:50',
            'sort'     => 'required|integer',
        ];
    }

    public function message()
    {
        return [

        ];
    }

}
