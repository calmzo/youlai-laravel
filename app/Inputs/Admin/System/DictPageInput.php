<?php

namespace App\Inputs\Admin\System;

use App\Inputs\PageInput;

class DictPageInput extends PageInput
{
    public $keywords;

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
