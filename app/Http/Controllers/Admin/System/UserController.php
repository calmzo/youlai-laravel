<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Admin\BaseController;
use App\Inputs\Admin\System\EmailBindingForm;
use App\Inputs\Admin\System\PasswordChangeFormInput;
use App\Inputs\Admin\System\UserFormInput;
use App\Inputs\Admin\System\UserPageInput;
use App\Inputs\Admin\System\UserProfileFormInput;
use App\Services\System\UserService;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    public $except = ['saveUser'];


    /**
     * 获取登录用户信息
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/6/19 15:52
     */
    public function getCurrentUserInfo()
    {
        $uid = $this->userId();
        $user = UserService::getInstance()->getCurrentUserInfo($uid);
        return $this->success($user);
    }


    /**
     * 用户分页列表
     * @return array|\Illuminate\Http\JsonResponse|mixed
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/19 15:52
     */
    public function listPagedUsers()
    {
        $input    = UserPageInput::new();
        $paginate = UserService::getInstance()->listPagedUsers($input);
        return $this->successPaginate($paginate);
    }

    /**
     * 导出用户
     * @logAnnotation('导出系统用户','SYSTEM_USER')
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/24 11:25
     */
    public function exportUsers()
    {
        $input    = UserPageInput::new();
        $res = UserService::getInstance()->listExportUsers($input);
        return $this->success($res);
    }

    /**
     * 导入用户
     * @logAnnotation('导入系统用户','SYSTEM_USER')
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/6/24 15:39
     */
    public function importUsers(Request $request)
    {
        $deptId = $request->input('deptId');
        $file = $request->file('file');
        $res = UserService::getInstance()->importUsers($deptId, $file);
        return $this->success($res);
    }


    /**
     * 用户导入模板下载
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/24 12:04
     */
    public function downloadTemplate()
    {
        $input    = UserPageInput::new();
        $res = UserService::getInstance()->downloadTemplate($input);
        return $this->success($res);
    }



    /**
     * 新增用户
     * @permission('hasPermi','sys:user:add')
     * @logAnnotation('新增系统用户','SYSTEM_USER')
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 12:03
     */
    public function saveUser()
    {
        $input = UserFormInput::new();
        $user = UserService::getInstance()->saveUser($input);
        return $this->success(true);
    }

    /**
     * 修改用户
     * @permission('hasPermi','sys:user:edit')
     * @logAnnotation('修改系统用户','SYSTEM_USER')
     * @param $userId
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 12:03
     */
    public function updateUser($userId)
    {
        $input = UserFormInput::new();
        $user = UserService::getInstance()->updateUser($userId, $input);
        return $this->success(true);
    }


    /**
     * 获取用户表单数据
     * @param $userId
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 10:32
     */
    public function getUserFormData($userId)
    {
        $list = UserService::getInstance()->getUserFormData($userId);
        return $this->success($list);

    }

    /**
     * 获取个人中心用户信息
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/8/19 13:36
     */
    public function getUserProfile()
    {
        $list = UserService::getInstance()->getUserProfile();
        return $this->success($list);

    }


    /**
     * 修改个人中心用户信息
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/8/19 13:52
     */
    public function updateUserProfile()
    {
        $input = UserProfileFormInput::new();
        $res = UserService::getInstance()->updateUserProfile($input);
        return $this->success($res);
    }



    /**
     * 删除用户
     * @permission('hasPermi','sys:user:delete')
     * @logAnnotation('删除系统用户','SYSTEM_USER')
     * @param $ids
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/6/20 10:43
     */
    public function deleteUsers($ids)
    {
        $res = UserService::getInstance()->deleteUsers($ids);
        return $this->success($res);
    }


    /**
     * 修改用户密码
     * @logAnnotation('修改系统用户密码','SYSTEM_USER')
     * @param $userId
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 10:47
     */
    public function updatePassword($userId)
    {
        $password = $this->verifyString('password');
        $res = UserService::getInstance()->updatePassword($userId, $password);
        return $this->success($res);
    }


    /**
     * @logAnnotation('修改系统用户状态','SYSTEM_USER')
     *
     * @param $userId
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/7/31 20:10
     */
    public function updateUserStatus($userId)
    {
        $status = $this->verifyString('status');
        $res = UserService::getInstance()->updateUserStatus($userId, $status);
        return $this->success($res);
    }

    /**
     * 重置用户密码
     *
     * @param $userId
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/8/19 16:29
     */
    public function resetPassword($userId)
    {
        $password = $this->verifyString('password');
        $res = UserService::getInstance()->resetPassword($userId, $password);
        return $this->success($res);
    }


    /**
     * 修改用户密码
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/8/19 16:29
     */
    public function changePassword()
    {
        $userId = $this->userId();
        $input = PasswordChangeFormInput::new();
        $res = UserService::getInstance()->changePassword($userId, $input);
        return $this->success($res);
    }


    /**
     * 回收站用户分页列表
     * @permission('hasPermi','sys:userRecycle:query')
     * @return array|\Illuminate\Http\JsonResponse|mixed
     * @throws \App\Exceptions\BusinessException
     * @author 2024/8/7 10:48
     */
    public function listPagedRecycleUsers()
    {
        $input    = UserPageInput::new();
        $paginate = UserService::getInstance()->listPagedRecycleUsers($input);
        return $this->successPaginate($paginate);
    }

    /**
     * 还原回收站用户
     * @permission('hasPermi','sys:userRecycle:restore')
     * @logAnnotation('还原系统用户回收站数据','SYSTEM_USER')
     * @param $ids
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/8/7 10:51
     */
    public function restoreRecycleUser($ids)
    {
        $res = UserService::getInstance()->restoreRecycleUser($ids);
        return $this->success($res);
    }


    /**
     * 删除回收站用户
     * @permission('hasPermi','sys:userRecycle:delete')
     * @logAnnotation('删除系统用户回收站数据','SYSTEM_USER')
     * @param $ids
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/8/7 11:02
     */
    public function deleteRecycleUsers($ids)
    {
        $res = UserService::getInstance()->deleteRecycleUsers($ids);
        return $this->success($res);
    }


    public function listUserOptions()
    {
        $menus = UserService::getInstance()->listUserOptions();
        return $this->success($menus);
    }

    public function bindEmail()
    {
        $input = EmailBindingFormInput::new();
        $res   = UserService::getInstance()->bindEmail($input);
        return $this->success($res);
    }
}
