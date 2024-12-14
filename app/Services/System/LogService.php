<?php

namespace App\Services\System;

use App\Enums\SysLogEnum;
use App\Inputs\Admin\System\LogPageInput;
use App\Models\System\SysLog;
use App\Services\BaseService;
use App\Utils\CodeResponse;

class LogService extends BaseService
{


    /**
     * 日志列表
     *
     * @param LogPageInput $input
     * @param string[] $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @author 2024/7/30 18:28
     */
    public function listPagedLogs(LogPageInput $input, $columns = ['*'])
    {
        $columns = [
            'id', 'province', 'city', 'content', 'ip', 'os', 'browser', 'browser_version', 'create_time', 'module', 'execution_time', 'request_params', 'response_content', 'create_by'
        ];
        $page    = SysLog::query()
            ->with([
                'operatorUser' => function ($query) {
                    $query->select('id', 'nickname');
                }
            ])
            ->when($input->keywords, function ($query, $keywords) {
                $query->where('content', 'like', "%{$keywords}%");
            })
            ->orderBy($input->sort, $input->order)
            ->paginate($input->pageSize, $columns, 'page', $input->pageNum);
        $page->each(function ($item) {
            $item->region   = $item->province . ' ' . $item->city;
            $item->module   = SysLogEnum::moduleMap($item->module);
            $item->browser  = $item->browser . ' ' . $item->browser_version;
            $item->message  = $item->content; //暂时用
            $item->operator = $item->operatorUser->nickname ?? ''; //暂时用
        });
        return $page;
    }
}

