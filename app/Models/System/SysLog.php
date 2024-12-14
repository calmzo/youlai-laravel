<?php

namespace App\Models\System;

use App\Models\BaseModel;

class SysLog extends BaseModel
{
    const UPDATED_AT = null;

    /**
     * 类型转换。
     *
     * @var array
     */
    protected $casts = [
        'request_params' => 'array',
        'response_content' => 'array',
    ];

    /**
     * 操作人
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @author 2024/12/13 17:13
     */
    public function operatorUser()
    {
        return $this->belongsTo(SysUser::class, 'create_by');
    }
}
