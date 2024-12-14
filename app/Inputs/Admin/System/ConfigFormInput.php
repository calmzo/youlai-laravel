<?php

namespace App\Inputs\Admin\System;

use App\Inputs\Input;

class ConfigFormInput extends Input
{
    public $id; //ID
    public $configName; //配置名称
    public $configKey; //配置key
    public $configValue; //配置value
    public $remark; //备注

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
