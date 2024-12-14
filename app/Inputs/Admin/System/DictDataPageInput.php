<?php

namespace App\Inputs\Admin\System;

use App\Inputs\PageInput;

class DictDataPageInput extends PageInput
{
    public $dictCode;

    public function rule()
    {
        return [
            'dictCode'    => 'string',
        ];
    }

    public function message()
    {
        return [

        ];
    }

}
