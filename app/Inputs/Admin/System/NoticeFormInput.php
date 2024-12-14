<?php

namespace App\Inputs\Admin\System;

use App\Inputs\Input;

class NoticeFormInput extends Input
{
    public $id;
    public $content; //
    public $level; //
    public $targetType; //
    public $title; //
    public $type; //
    public $targetUserIds; //

    public function rule()
    {
        return [
            'content'    => 'required |string',
            'level'      => 'required |string',
            'title'      => 'required |string',
            'type'       => 'required |string',
            'targetType' => 'required |integer',
            'targetUserIds' => 'required |array',
        ];
    }

    public function message()
    {
        return [

        ];
    }

}
