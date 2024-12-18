<?php

namespace App\Services\System;

use App\Enums\SysLogEnum;
use App\Inputs\Admin\System\LogPageInput;
use App\Models\System\SysLog;
use App\Services\BaseService;
use App\Tools\Helpers;
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

    /**
     * 获取访问趋势
     *
     * @param $start 开始时间
     * @param $end 结束时间
     * @return array
     * @author 2024/12/18 18:37
     */
    public function getVisitTrend($start, $end)
    {
        $dates = Helpers::periodDate($start, $end);

        //统计浏览数(PV)
        $pvCounts = SysLog::query()
            ->selectRaw('COUNT(1) as count,DATE_FORMAT(create_time, "%Y-%m-%d") as date')
            ->where('is_deleted', 0)
            ->whereBetween('create_time', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->groupBy('date')
            ->get();

        //统计IP数
        $ipCounts = SysLog::query()
            ->selectRaw('COUNT(DISTINCT ip) as count, DATE_FORMAT(create_time, "%Y-%m-%d") as date')
            ->where('is_deleted', 0)
            ->whereBetween('create_time', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->groupBy('date')
            ->get();

        $pvMap = [];
        foreach ($pvCounts as $item) {
            $pvMap[$item['date']] = $item['count'];
        }

        $ipMap = [];
        foreach ($ipCounts as $item) {
            $ipMap[$item['date']] = $item['count'];
        }

        $pvList = [];
        $ipList = [];
        foreach ($dates as $date) {
            $pvList[] = $pvMap[$date] ?? 0;
            $ipList[] = $ipMap[$date] ?? 0;
        }

        $data = [
            'dates'  => $dates,
            'ipList' => $ipList,
            'pvList' => $pvList,
        ];
        return $data;

    }


    /**
     * 访问量统计
     *
     * @return array
     * @author 2024/12/18 18:36
     */
    public function getVisitStats()
    {
        //获取浏览量(PV)统计
        $uvStats = SysLog::query()
            ->selectRaw('COUNT(DISTINCT CASE WHEN DATE(create_time) = CURDATE() THEN ip END) AS todayCount,
            COUNT(DISTINCT ip) AS totalCount,
            ROUND(
                CASE
                    WHEN COUNT(DISTINCT CASE WHEN DATE(create_time) = CURDATE() - INTERVAL 1 DAY AND TIME(create_time) <= TIME(NOW()) THEN ip END) = 0 THEN 0
                    ELSE
                        (COUNT(DISTINCT CASE WHEN DATE(create_time) = CURDATE() THEN ip END) -
                         COUNT(DISTINCT CASE WHEN DATE(create_time) = CURDATE() - INTERVAL 1 DAY AND TIME(create_time) <= TIME(NOW()) THEN ip END)) /
                        COUNT(DISTINCT CASE WHEN DATE(create_time) = CURDATE() - INTERVAL 1 DAY AND TIME(create_time) <= TIME(NOW()) THEN ip END)
                    END,
                2) AS growthRate')
            ->where('is_deleted', 0)
            ->first();

        //获取访问IP统计
        $pvStats = SysLog::query()
            ->selectRaw('COUNT(CASE WHEN DATE(create_time) = CURDATE() THEN 1 END) AS todayCount,
            COUNT(*) AS totalCount,
            ROUND(
                CASE
                    WHEN COUNT(CASE WHEN DATE(create_time) = CURDATE() - INTERVAL 1 DAY AND TIME(create_time) <= TIME(NOW()) THEN 1 END) = 0 THEN 0
                ELSE
                    (COUNT(CASE WHEN DATE(create_time) = CURDATE() THEN 1 END) -
                    COUNT(CASE WHEN DATE(create_time) = CURDATE() - INTERVAL 1 DAY AND TIME(create_time)  <= TIME(NOW()) THEN 1 END)) /
                    COUNT(CASE WHEN DATE(create_time) = CURDATE() - INTERVAL 1 DAY AND TIME(create_time)  <= TIME(NOW()) THEN 1 END)
                END,
            2) AS growthRate')
            ->where('is_deleted', 0)
            ->first();
        $data    = [
            'todayUvCount' => $uvStats->todayCount ?? 0,
            'totalUvCount' => $uvStats->totalCount ?? 0,
            'uvGrowthRate' => $uvStats->growthRate ?? 0,
            'todayPvCount' => $pvStats->todayCount ?? 0,
            'totalPvCount' => $pvStats->totalCount ?? 0,
            'pvGrowthRate' => $pvStats->growthRate ?? 0,
        ];
        return $data;


    }
}

