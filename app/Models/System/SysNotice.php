<?php

namespace App\Models\System;
use App\Models\BaseModel;

class SysNotice extends BaseModel
{
    public $fillable = [];

    /**
     * 类型转换。
     *
     * @var array
     */
    protected $casts = [
        'target_user_ids' => 'array',
    ];

    public function publishUser()
    {
        return $this->belongsTo(SysUser::class, 'publisher_id');
    }
}
