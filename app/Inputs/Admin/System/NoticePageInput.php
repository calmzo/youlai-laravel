<?php

namespace App\Inputs\Admin\System;

use App\Inputs\PageInput;

class NoticePageInput extends PageInput
{
    public $title;
    public $publishStatus;
    public $isRead;

    public function rule()
    {
        return [
            'title'  => 'string',
        ];
    }

    public function message()
    {
        return [

        ];
    }

}
