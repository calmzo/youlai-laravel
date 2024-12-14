<?php

namespace App\Models\System;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SysDict extends BaseModel
{

    public $fillable = [];

    public function dictDataList()
    {
        return $this->hasMany(SysDictData::class, 'dict_code', 'dict_code');
    }

}
