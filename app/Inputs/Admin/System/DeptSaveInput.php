<?php

namespace App\Inputs\Admin\System;

use App\Inputs\PageInput;

class DeptSaveInput extends PageInput
{
    public $id;
    public $name;
    public $parentId;
    public $status;
    public $sort;


    public function rule()
    {
        return [
        ];
    }

    public function message()
    {
        return [

        ];
    }

}
