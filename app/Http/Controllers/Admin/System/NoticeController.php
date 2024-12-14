<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Admin\BaseController;
use App\Inputs\Admin\System\NoticeFormInput;
use App\Inputs\Admin\System\NoticePageInput;
use App\Services\System\NoticeService;
use App\Services\System\UserNoticeService;

class NoticeController extends BaseController
{
    public $except = [];

    /**
     * 获取通知公告分页列表
     * @permission('hasPermi','sys:notice:query')
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/12/13 16:59
     */
    public function getNoticePage()
    {
        $input    = NoticePageInput::new();
        $paginate = NoticeService::getInstance()->getNoticePage($input);
        return $this->successPaginate($paginate);
    }

    /**
     * 获取我的通知公告分页列表
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/12/13 17:06
     */
    public function getMyNoticePage()
    {
        $input    = NoticePageInput::new();
        $paginate = UserNoticeService::getInstance()->getMyNoticePage($input);
        return $this->successPaginate($paginate);
    }

    /**
     * 获取通知公告表单数据
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/12/13 17:01
     */
    public function getNoticeForm($id)
    {
        $form = NoticeService::getInstance()->getNoticeForm($id);
        return $this->success($form);
    }

    /**
     * 阅读获取通知公告详情
     *
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/12/13 17:07
     */
    public function getNoticeDetail($id)
    {
        $list = NoticeService::getInstance()->getNoticeDetail($id);
        return $this->success($list);
    }

    /**
     * @permission('hasPermi','sys:notice:add')
     * @logAnnotation('新增通知公告','SYSTEM_NOTICE')
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/12/13 17:03
     */
    public function saveNotice()
    {
        $input  = NoticeFormInput::new();
        $config = NoticeService::getInstance()->saveNotice($input);
        return $this->success($config);
    }


    /**
     * @permission('hasPermi','sys:notice:edit')
     * @logAnnotation('修改通知公告','SYSTEM_NOTICE')
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/12/13 17:04
     */
    public function updateNotice($id)
    {
        $input  = NoticeFormInput::new();
        $config = NoticeService::getInstance()->updateNotice($id, $input);
        return $this->success($config);
    }


    /**
     * @permission('hasPermi','sys:notice:publish')
     * @logAnnotation('发布通知公告','SYSTEM_NOTICE')
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/12/13 17:04
     */
    public function publishNotice($id)
    {
        $config = NoticeService::getInstance()->publishNotice($id);
        return $this->success($config);
    }


    /**
     * @permission('hasPermi','sys:notice:revoke')
     * @logAnnotation('撤回通知公告','SYSTEM_NOTICE')
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/12/13 17:05
     */
    public function revokeNotice($id)
    {
        $config = NoticeService::getInstance()->revokeNotice($id);
        return $this->success($config);
    }

    /**
     * @permission('hasPermi','sys:notice:delete')
     * @logAnnotation('删除通知公告','SYSTEM_NOTICE')
     * @param $ids
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/12/13 17:05
     */
    public function deleteNotices($ids)
    {
        $res = NoticeService::getInstance()->deleteNotices($ids);
        return $this->success($res);
    }

    /**
     * 全部已读
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/12/13 17:06
     */
    public function readAll()
    {
        $res = UserNoticeService::getInstance()->readAll();
        return $this->success($res);
    }
}
