<?php

namespace App\Models\System;

use App\Models\BaseModel;

class SysDept extends BaseModel
{
    public $fillable = [];

    public function menus()
    {
        return $this->belongsToMany(SysMenu::class, 'sys_role_menu','role_id', 'menu_id');
    }

}
