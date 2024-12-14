<?php

namespace App\Models\System;
use App\Models\BaseModel;

class SysUserNotice extends BaseModel
{
    public $fillable = [];

    public function notice()
    {
        return $this->belongsTo(SysNotice::class, 'notice_id');
    }

    public function user()
    {
        return $this->belongsTo(SysUser::class, 'user_id');
    }
}
