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

    /**
     * 获取访问趋势
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/12/18 18:38
     */
    public function getVisitTrend()
    {
        $start = $this->verifyString('startDate');
        $end = $this->verifyString('endDate');
        $data = LogService::getInstance()->getVisitTrend($start, $end);
        return $this->success($data);
    }

    /**
     * 获取访问统计
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/12/18 18:38
     */
    public function getVisitStats()
    {
        $data = LogService::getInstance()->getVisitStats();
        return $this->success($data);
    }

}
