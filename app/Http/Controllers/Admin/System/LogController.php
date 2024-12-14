<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Admin\BaseController;
use App\Inputs\Admin\System\LogPageInput;
use App\Services\System\LogService;

class LogController extends BaseController
{
    public $except = [];


    /**
     * 日志分页列表
     *
     * @return array|\Illuminate\Http\JsonResponse|mixed
     * @throws \App\Exceptions\BusinessException
     * @author 2024/7/30 18:43
     */
    public function listPagedLogs()
    {
        $input    = LogPageInput::new();
        $paginate = LogService::getInstance()->listPagedLogs($input);
        return $this->successPaginate($paginate);
    }

}
