<?php

namespace App\Inputs\Admin\System;

use App\Inputs\PageInput;

class DeptInput extends PageInput
{
    public $keywords;
    public $status;


    public function rule()
    {
        return [
            'keywords'    => 'string',
        ];
    }

    public function message()
    {
        return [

        ];
    }

}
