<?php

namespace App\Services\System;

use App\Inputs\Admin\System\NoticePageInput;
use App\Models\System\SysUserNotice;
use App\Services\BaseService;

class UserNoticeService extends BaseService
{

    /**
     * 全部标记为已读
     *
     * @return bool|int 是否成功
     * @author 2024/12/12 17:25
     */
    public function readAll()
    {
        $userId = LoginService::getInstance()->userId();
        $res = SysUserNotice::query()->where('user_id', $userId)->where('is_read', 0)->update(['is_read' => 1]);
        return $res;
    }


    /**
     * 获取我的通知公告分页列表
     *
     * @param NoticePageInput $input
     * @param string[] $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @author 2024/12/13 17:10
     */
    public function getMyNoticePage(NoticePageInput $input, $columns = ['*'])
    {
        $columns  = ['id', 'is_read', 'user_id', 'notice_id'];
        $userId = LoginService::getInstance()->userId();
        $query    = SysUserNotice::query();
        $page = $query
            ->with('notice')
            ->where('user_id', $userId)
            ->whereHas('notice', function ($query) use ($userId) {
                $query->where('publish_status', 1);
            })
            ->when($input->title, function ($query, $title) {
                $query->where('title', 'like', "%{$title}%");
            })
            ->when($input->isRead || $input->isRead === '0', function ($query, $isRead) {
                $query->where('is_read', $isRead);
            })
            ->orderBy($input->sort, $input->order)
            ->paginate($input->pageSize, $columns, 'page', $input->pageNum);
        $page->each(function ($item) {
            $item->id = $item->notice->id ?? '';
            $item->title = $item->notice->title ?? '';
            $item->publish_time = $item->notice->publish_time ?? '';
            $item->publisher_name = $item->notice->publishUser->nickname ?? '';
            $item->level = $item->notice->level ?? '';
            $item->type = $item->notice->type ?? '';
        });
        return $page;
    }

}
