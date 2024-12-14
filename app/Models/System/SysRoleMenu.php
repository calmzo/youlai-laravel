<?php

namespace App\Models\System;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SysRoleMenu extends Model
{

    public $fillable = [];

    /**
     * 表名约定
     * @return string
     */
    public function getTable()
    {
        return $this->table ?? Str::snake(class_basename($this));
    }


    public function menu()
    {
        return $this->belongsTo(SysMenu::class,'menu_id');
    }

    public function role()
    {
        return $this->belongsTo(SysRole::class, 'role_id');
    }

}
