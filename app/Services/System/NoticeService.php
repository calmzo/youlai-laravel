<?php

namespace App\Services\System;

use App\Enums\SysNoticeEnum;
use App\Inputs\Admin\System\NoticeFormInput;
use App\Inputs\Admin\System\NoticePageInput;
use App\Models\System\SysNotice;
use App\Models\System\SysUser;
use App\Models\System\SysUserNotice;
use App\Services\BaseService;
use App\Utils\CodeResponse;

class NoticeService extends BaseService
{

    /**
     * 获取通知公告分页列表
     *
     * @param NoticePageInput $input
     * @param string[] $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @author 2024/12/13 17:08
     */
    public function getNoticePage(NoticePageInput $input, $columns = ['*'])
    {
        $columns = ['id', 'title', 'type', 'level', 'target_type', 'publish_status', 'publish_time', 'revoke_time', 'create_time', 'publisher_id'];
        $query   = SysNotice::query();
        $page    = $query
            ->with('publishUser')
            ->when($input->title, function ($query, $title) {
                $query->where('title', 'like', "%{$title}%");
            })
            ->when($input->publishStatus, function ($query, $publishStatus) {
                $query->where('publish_status', $publishStatus);
            })
            ->orderByDesc('publish_time')
            ->orderByDesc('create_time')
            ->paginate($input->pageSize, $columns, 'page', $input->pageNum);
        $page->each(function ($item) {
            $item->publisher_name = $item->publishUser->nickname ?? '';
        });
        return $page;
    }

    /**
     * 获取通知公告表单数据
     *
     * @param $id
     * @return \App\Models\BaseModel|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     * @throws \App\Exceptions\BusinessException
     * @author 2024/12/13 17:08
     */
    public function getNoticeForm($id)
    {
        $columns = ['id', 'title', 'type', 'level', 'target_type', 'publish_status', 'publish_time', 'revoke_time', 'create_time', 'publisher_id', 'content'];
        $form    = SysNotice::query()->where('id', $id)->first($columns);
        $form->publisher_name = $form->publishUser->nickname ?? '';
        if (is_null($form)) {
            //数据项不存在
            $this->throwBusinessException();
        }
        return $form;
    }

    /**
     * 阅读获取通知公告详情
     *
     * @param $id
     * @return \App\Models\BaseModel|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     * @throws \App\Exceptions\BusinessException
     * @author 2024/12/13 17:09
     */
    public function getNoticeDetail($id)
    {
        $userId = LoginService::getInstance()->userId();
        SysUserNotice::query()->where('notice_id', $id)->where('user_id', $userId)->where('is_read', 0)->update(['is_read' => 1]);
        $form = $this->getNoticeForm($id);
        return $form;
    }

    /**
     * 新增通知公告
     *
     * @param NoticeFormInput $input
     * @return bool
     * @throws \App\Exceptions\BusinessException
     * @author 2024/12/13 17:09
     */
    public function saveNotice(NoticeFormInput $input)
    {
        if ($input->targetType == SysNoticeEnum::TARGET_TYPE_SPECIFIED) {
            if (!$input->targetUserIds) {
                $this->throwBusinessException(CodeResponse::SYSTEM_EXECUTION_ERROR, '推送指定用户不能为空');
            }
        }
        $notice                  = SysNotice::new();
        $notice->content         = $input->content;
        $notice->target_type     = $input->targetType;
        $notice->level           = $input->level;
        $notice->title           = $input->title;
        $notice->type            = $input->type;
        $notice->target_user_ids = $input->targetUserIds;
        $notice->create_by       = LoginService::getInstance()->userId();
        $result                  = $notice->save();
        return $result;
    }


    /**
     * 修改通知公告
     *
     * @param $id
     * @param NoticeFormInput $input
     * @return bool
     * @throws \App\Exceptions\BusinessException
     * @author 2024/12/13 17:09
     */
    public function updateNotice($id, NoticeFormInput $input)
    {
        if ($input->targetType == SysNoticeEnum::TARGET_TYPE_SPECIFIED) {
            if (!$input->targetUserIds) {
                $this->throwBusinessException(CodeResponse::SYSTEM_EXECUTION_ERROR, '推送指定用户不能为空');
            }
        }
        $notice              = SysNotice::query()->find($id);
        $notice->content     = $input->content;
        $notice->target_type = $input->targetType;
        $notice->level       = $input->level;
        $notice->title       = $input->title;
        $notice->type        = $input->type;
        return true;
    }

    /**
     * 发布通知公告
     *
     * @param $id
     * @return bool
     * @throws \App\Exceptions\BusinessException
     * @author 2024/12/13 17:09
     */
    public function publishNotice($id)
    {

        $notice = SysNotice::query()->where('id', $id)->first();
        if (is_null($notice)) {
            //数据项不存在
            $this->throwBusinessException();
        }
        if ($notice->publish_status == SysNoticeEnum::PUBLISH_STATUS_PUBLISHED) {
            $this->throwBusinessException(CodeResponse::SYSTEM_EXECUTION_ERROR, '通知公告已发布');
        }
        if ($notice->target_type == SysNoticeEnum::TARGET_TYPE_SPECIFIED) {
            if (!$notice->target_user_ids) {
                $this->throwBusinessException(CodeResponse::SYSTEM_EXECUTION_ERROR, '推送指定用户不能为空');
            }
        }
        $notice->publish_status = SysNoticeEnum::PUBLISH_STATUS_PUBLISHED;
        $notice->publisher_id   = LoginService::getInstance()->userId();
        $notice->publish_time   = date('Y-m-d H:i:s');
        $publishResult          = $notice->save();
        if ($publishResult) {
            // 发布通知公告的同时，删除该通告之前的用户通知数据，因为可能是重新发布
            SysUserNotice::query()->where('notice_id', $id)->delete();
            // 添加新的用户通知数据
            $targetUserIdList = null;
            if ($notice->target_type == SysNoticeEnum::TARGET_TYPE_SPECIFIED) {
                $targetUserIdList = $notice->target_user_ids;
            }
            $targetUserList = SysUser::query()->when($targetUserIdList, function ($query, $targetUserIdList) {
                $query->whereIn('id', $targetUserIdList);
            })->get();
            $userNoticeData = [];
            $time           = date('Y-m-d H:i:s');
            foreach ($targetUserList as $user) {
                $userNoticeData[] = [
                    'user_id'     => $user->id,
                    'notice_id'   => $notice->id,
                    'is_read'     => 0,
                    'create_time' => $time,
                    'update_time' => $time,
                ];
            }
            SysUserNotice::query()->insert($userNoticeData);
            // 找出在线用户的通知接收者 todo websocket推送

        }
        return $publishResult;
    }


    /**
     * 撤回通知公告
     *
     * @param $id
     * @return bool
     * @throws \App\Exceptions\BusinessException
     * @author 2024/12/13 17:10
     */
    public function revokeNotice($id)
    {

        $notice = SysNotice::query()->where('id', $id)->first();
        if (is_null($notice)) {
            //数据项不存在
            $this->throwBusinessException();
        }
        if ($notice->publish_status != SysNoticeEnum::PUBLISH_STATUS_PUBLISHED) {
            $this->throwBusinessException(CodeResponse::SYSTEM_EXECUTION_ERROR, '通知公告未发布或已撤回');
        }

        $notice->publish_status = SysNoticeEnum::PUBLISH_STATUS_REVOKED;
        $notice->revoke_time    = date('Y-m-d H:i:s');
        $notice->update_by      = LoginService::getInstance()->userId();
        $revokeResult           = $notice->save();
        if ($revokeResult) {
            // 撤回通知公告的同时，需要删除通知公告对应的用户通知状态
            SysUserNotice::query()->where('notice_id', $id)->delete();
        }
        return $revokeResult;
    }

    /**
     * 删除通知公告
     *
     * @param $idsStr
     * @return int|mixed
     * @throws \App\Exceptions\BusinessException
     * @author 2024/12/13 17:10
     */
    public function deleteNotices($idsStr)
    {
        if (!$idsStr) {
            //删除数据为空
            $this->throwBusinessException();
        }
        $ids       = explode(',', $idsStr);
        $isRemoved = SysNotice::query()->whereIn('id', $ids)->delete();
        if ($isRemoved) {
            SysUserNotice::query()->whereIn('notice_id', $ids)->delete();
        }
        return $isRemoved;
    }


}
