<?php

namespace App\Models\System;
use App\Models\BaseModel;

class SysRole extends BaseModel
{
    public $fillable = [];

    public function menus()
    {
        return $this->belongsToMany(SysMenu::class, 'sys_role_menu','role_id', 'menu_id');
    }

    public function users()
    {
        return $this->belongsToMany(SysUser::class, 'sys_user_role','role_id', 'user_id');
    }
}
