<?php

namespace App\Services\System;

use App\Exceptions\BusinessException;
use App\Inputs\Admin\System\EmailBindingFormInput;
use App\Inputs\Admin\System\PasswordChangeFormInput;
use App\Inputs\Admin\System\UserFormInput;
use App\Inputs\Admin\System\UserPageInput;
use App\Inputs\Admin\System\UserProfileFormInput;
use App\Lib\Excel\Export\UserExport;
use App\Lib\Excel\Import\UserImport;
use App\Models\System\SysUser as User;
use App\Services\BaseService;
use App\Tools\Helpers;
use App\Utils\{CodeResponse, Constant, ConstantEnum, Helper, RedisConstant};
use Illuminate\Support\Facades\{Cache, Hash};


class UserService extends BaseService
{

    public function getByUsername(string $username)
    {
        return User::query()->where('username', $username)->first();
    }


    /**
     * 获取登录用户信息
     *
     * @param $uid
     * @return \App\Models\BaseModel|User|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     * @author 2024/6/19 15:47
     */
    public function getCurrentUserInfo($uid)
    {
        $user  = User::query()->with('roles')->where('id', $uid)->first(['id', 'username', 'nickname', 'avatar']);
        $roles = $user->roles->pluck('code')->toArray();
        unset($user->roles);
        $user->roles = $roles;
        $perms       = PermissionService::getInstance()->getRolePermsFormCache($roles);
        $user->perms = $perms;
        return $user;
    }


    /**
     * 列表
     *
     * @param UserPageInput $input
     * @param string[] $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @author 2023/12/8 19:02
     */
    public function listPagedUsers(UserPageInput $input, $columns = ['*'])
    {
        $query = User::query()->with(['dept', 'roles']);
        $query = $this->getUserQuery(
            $query,
            $input
        );
        $page  = $query
            ->where('username', '<>', 'root')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->pageSize, $columns, 'page', $input->pageNum);

        $page->each(function ($item) {
            $item->dept_name = $item->dept->name ?? '';
            $item->gender_label = ConstantEnum::genderMap($item->gender);
            $item->role_names = $item->roles->pluck('name')->toArray();
            $item->role_names = implode(',', $item->roles->pluck('name')->toArray());
            $item->role_name_list = $item->roles->pluck('name');
            $item->role_id_list = $item->roles->pluck('id');
            unset($item->dept);
            unset($item->roles);
        });

        return $page;
    }


    /**
     * 导出用户列表
     *
     * @param UserPageInput $input
     * @param string[] $columns
     * @return void
     * @author 2024/6/24 11:21
     */
    public function listExportUsers(UserPageInput $input, $columns = ['*'])
    {
        $query = User::query()->with(['dept', 'roles']);
        $query = $this->getUserQuery(
            $query,
            $input
        );
        $list  = $query
            ->where('username', '<>', 'root')
            ->orderBy($input->sort, $input->order)
            ->get($columns);
        foreach ($list as $item) {
            $item->dept_name = $item->dept->name ?? '';
            $item->gender_label = ConstantEnum::genderMap($item->gender);
            $item->role_names = $item->roles->pluck('name')->toArray();
            $item->role_names = implode(',', $item->roles->pluck('name')->toArray());
            unset($item->dept);
            unset($item->roles);
        }
        $export = new UserExport();
        $res = $export->export($list);

        return $res;
    }


    /**
     * 导入用户
     *
     * @param $deptId
     * @param $file
     * @return false
     * @author 2024/6/24 15:39
     */
    public function importUsers($deptId, $file)
    {
        $import = new UserImport();
        $res = $import->import(['deptId' => $deptId, 'file' => $file]);
        return $res;
    }

    public function downloadTemplate(UserPageInput $input, $columns = ['*'])
    {
        $export = new UserExport();
        $res = $export->downLoad();

        return $res;
    }

    private function getUserQuery($query, UserPageInput $input)
    {

        $query->when($input->keywords, function ($query, $keywords) {
            $query->where('username', 'like', "%{$keywords}%")->orWhere('nickname', 'like', "%{$keywords}%")->orWhere('mobile', 'like', "%{$keywords}%");
        })->when($input->deptId, function ($query, $deptId) {
            $query->whereHasIn('dept', function ($query) use ($deptId) {
                $query->whereRaw("concat(',',concat(tree_path,',',id),',') like concat('%,',{$deptId},',%')");
            });
        })->when($input->status, function ($query, $status) {
            $query->where('status', $status);
        })->when($input->roleId, function ($query, $roleId) {
            $query->whereHasIn('roles', function ($query) use ($roleId) {
                $query->where('sys_user_role.role_id', $roleId);
            });
        })->when($input->createTime, function ($query, $createTime) {
            if (isset($createTime[0]) && isset($createTime[1])) {
                $startDate = strlen($createTime[0]) == 10 ? $createTime[0] . ' 00:00:00' : $createTime[0];
                $endData = strlen($createTime[1]) == 10 ? $createTime[1] . ' 23:59:59' : $createTime[1];
                $query->whereBetween('create_time', [strtotime($startDate), strtotime($endData)]);
            }
        });
        return $query;
    }


    public function saveUser(UserFormInput $input)
    {
        $username = $input->username;
        $count    = User::query()->where('username', $username)->count();
        if ($count > 0) {
            //用户名已存在
            $this->throwBusinessException();
        }
        //生产环境密码为用户名+#2024  测试环境密码为123456
        $password = env('APP_ENV') == 'prod' ? $input->username . '#2024' : Constant::DEFAULT_PASSWORD;

        $user = User::new();
        $user->username = $input->username;
        $user->nickname = $input->nickname;
        $user->gender   = $input->gender;
//        $user->password = Hash::make(Constant::DEFAULT_PASSWORD);
        $user->password = Hash::make($password);
        $user->dept_id  = $input->deptId;
        $user->avatar   = $input->avatar ?:'http://cdnwm.yuluojishu.com/c5ca2fb3e92a726d4dec641fc87c2f98.jpg';
        $user->mobile   = $input->mobile;
        $user->status   = $input->status;
        $user->email    = $input->email;
        $result         = $user->save();

        if ($result) {
            // 保存用户角色（添加关联）
            $user->roles()->attach($input->roleIds);
        }
        return $user;
    }

    public function updateUser($userId, UserFormInput $input)
    {
        $username = $input->username;
        $count    = User::query()->where('id', '<>', $userId)->where('username', $username)->count();
        if ($count > 0) {
            //用户名已存在
            $this->throwBusinessException();
        }
        $user = User::query()->where('id', $userId)->first();
        if (is_null($user)) {
            //用户不存在
            $this->throwBusinessException();
        }

        $user->username = $input->username;
        $user->nickname = $input->nickname;
        $user->gender   = $input->gender;
        $user->dept_id  = $input->deptId;
        $user->avatar   = $input->avatar ?: 'http://cdnwm.yuluojishu.com/c5ca2fb3e92a726d4dec641fc87c2f98.jpg';
        $user->mobile   = $input->mobile;
        $user->status   = $input->status;
        $user->email    = $input->email;
        $result         = $user->save();

        if ($result) {
            // 保存用户角色(更新关联)
            $user->roles()->sync($input->roleIds);
        }
        return $user;
    }


    /**
     * 获取用户表单数据
     *
     * @param $id
     * @return \App\Models\BaseModel|User|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 10:32
     */
    public function getUserFormData($id)
    {
        $column = ['id', 'username', 'nickname', 'mobile', 'gender', 'avatar', 'email', 'status', 'dept_id'];
        $form   = User::query()->where('id', $id)->with(['roles'])->first($column);
        if (is_null($form)) {
            //用户不存在
            $this->throwBusinessException();
        }
        $form->roleIds = $form->roles->pluck('id')->toArray();
        unset($form->roles);
        return $form;
    }


    /**
     * 获取个人中心用户信息
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     * @throws \App\Exceptions\BusinessException
     * @author 2024/8/19 13:36
     */
    public function getUserProfile()
    {
        $userId = LoginService::getInstance()->userId();
        $column = ['id', 'username', 'nickname', 'mobile', 'gender', 'avatar', 'email', 'status', 'dept_id', 'create_time'];
        $user   = User::query()->where('id', $userId)->first($column);
        if (is_null($user)) {
            //用户不存在
            $this->throwBusinessException();
        }
        $user->dept_name = $user->dept->name ?? '';
        $user->role_names = implode(',', $user->roles->pluck('name')->toArray());
        unset($user->dept, $user->roles);
        return $user;
    }


    /**
     * 修改个人中心用户信息
     *
     * @param $userId
     * @param UserProfileFormInput $input
     * @return bool
     * @throws \App\Exceptions\BusinessException
     * @author 2024/8/19 13:52
     */
    public function updateUserProfile(UserProfileFormInput $input)
    {
        $userId = LoginService::getInstance()->userId();
        $username = $input->username;
        $count    = User::query()->where('id', '<>', $userId)->where('username', $username)->count();
        if ($count > 0) {
            //用户名已存在
            $this->throwBusinessException();
        }
        $user = User::query()->where('id', $userId)->first();
        if (is_null($user)) {
            //用户不存在
            $this->throwBusinessException();
        }
        $user->username = $input->username ?: $user->username;
        $user->nickname = $input->nickname ?: $user->nickname;
        $user->gender   = $input->gender ?: $user->gender;
        $user->avatar   = $input->avatar ?: $user->avatar;
        $user->mobile   = $input->mobile ?: $user->mobile;
        $user->email    = $input->email ?: $user->email;
        $result         = $user->save();
        return $result;
    }


    /**
     * 删除用户
     *
     * @param $idsStr
     * @return bool
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 10:44
     */
    public function deleteUsers($idsStr)
    {
        if (!$idsStr) {
            //删除的用户数据为空
            $this->throwBusinessException();
        }
        $ids = explode(',', $idsStr);
        foreach ($ids as $id) {
            $user = User::query()->where('id', $id)->first();
            if ($user) {
                $user->roles()->detach();
            }
            $user->delete();
        }
        return true;
    }

    /**
     * 重置密码
     *
     * @param $userId
     * @param $password
     * @return int
     * @author 2024/8/19 16:17
     */
    public function resetPassword($userId, $password)
    {
        return User::query()->where('id', $userId)->update(['password' => Hash::make($password)]);
    }


    /**
     * 修改用户密码
     *
     * @param $userId
     * @param PasswordChangeFormInput $input
     * @return bool
     * @throws \App\Exceptions\BusinessException
     * @author 2024/8/19 16:28
     */
    public function changePassword($userId, PasswordChangeFormInput $input)
    {
       $user = User::query()->where('id', $userId)->first();
       $oldPassword = $input->oldPassword;
       $newPassword = $input->newPassword;
       if ($newPassword != $input->confirmPassword) {
           //确认密码错误
           $this->throwBusinessException(CodeResponse::USERNAME_OR_PASSWORD_ERROR, '确认密码错误');
       }

        // 校验原密码
        if (!Hash::check($oldPassword, $user->getAuthPassword())) {
            $this->throwBusinessException(CodeResponse::USERNAME_OR_PASSWORD_ERROR, '原密码错误');
        }
        // 新旧密码不能相同
        if (Hash::check($newPassword, $user->getAuthPassword())) {
            $this->throwBusinessException(CodeResponse::USERNAME_OR_PASSWORD_ERROR, '新密码不能与原密码相同');
        }
        $user->password = Hash::make($newPassword);
        return $user->save();
    }


    /**
     * 修改用户密码 准备弃用
     *
     * @param $userId
     * @param $password
     * @return mixed
     * @author 2024/6/20 10:47
     */
    public function updatePassword($userId, $password)
    {
        return User::query()->where('id', $userId)->update(['password' => Hash::make($password)]);
    }


    /**
     * 修改用户状态
     *
     * @param $userId
     * @param $status 用户状态(1:启用;0:禁用)
     * @return bool|int
     * @author 2024/6/20 10:52
     */
    public function updateUserStatus($userId, $status)
    {
        return User::query()->where('id', $userId)->update(['status' => $status]);
    }


    /**
     * 查看回收站用户列表
     *
     * @param UserPageInput $input
     * @param string[] $columns
     * @return mixed
     * @author 2024/8/7 10:48
     */
    public function listPagedRecycleUsers(UserPageInput $input, $columns = ['*'])
    {
        $query = User::query()->with(['dept', 'roles']);
        $query = $this->getUserQuery(
            $query,
            $input
        );
        $page  = $query
            ->where('username', '<>', 'root')
            ->orderBy($input->sort, $input->order)
            ->onlyTrashed()
            ->paginate($input->pageSize, $columns, 'page', $input->pageNum);

        $page->each(function ($item) {
            $item->deptName = $item->dept->name ?? '';
            $item->gender_label = ConstantEnum::genderMap($item->gender);
//            $item->role_names = $item->roles->pluck('name')->toArray();
            $item->role_names = implode(',', $item->roles->pluck('name')->toArray());
            unset($item->dept);
            unset($item->roles);
        });

        return $page;
    }

    /**
     * 还原回收站用户
     *
     * @param $idsStr
     * @return bool
     * @throws \App\Exceptions\BusinessException
     * @author 2024/8/7 10:51
     */
    public function restoreRecycleUser($idsStr)
    {
        if (!$idsStr) {
            //还原的用户数据为空
            $this->throwBusinessException();
        }
        $ids = explode(',', $idsStr);
        foreach ($ids as $id) {
            $user = User::query()->where('id', $id)->onlyTrashed()->first();
            $user->restore();
        }
        return true;
    }


    /**
     * 删除回收站用户
     *
     * @param $idsStr
     * @return bool
     * @throws \App\Exceptions\BusinessException
     * @author 2024/8/7 11:01
     */
    public function deleteRecycleUsers($idsStr)
    {
        if (!$idsStr) {
            //删除回收站的用户数据为空
            $this->throwBusinessException();
        }
        $ids = explode(',', $idsStr);
        foreach ($ids as $id) {
            $user = User::query()->where('id', $id)->onlyTrashed()->first();
            $user->forceDelete();
        }
        return true;
    }

    public function getByMobile($mobile)
    {
        return User::query()->where('mobile', $mobile)->first();
    }


    public function listUserOptions()
    {
        $userList = User::query()->select()->get(['id', 'nickname']);
        return Helpers::model2Options($userList->toArray(), 'id', 'nickname');
    }

    /**
     * 修改当前用户邮箱
     *
     * @param EmailBindingFormInput $input
     * @return bool|int
     * @throws BusinessException
     * @author 2025/1/2 19:51
     */
    public function bindEmail(EmailBindingFormInput $input)
    {

        $userId = LoginService::getInstance()->userId();
        $email = $input->email;
        $inputVerificationCode = $input->code;
        $key    = RedisConstant::EMAIL_VERIFICATION_CODE_PREFIX . $email;
        $cachedVerificationCode = Cache::get($key);
        if (!$inputVerificationCode || $inputVerificationCode != $cachedVerificationCode) {
            throw new BusinessException(CodeResponse::VERIFY_CODE_ERROR);
        }
        return User::query()->where('id', $userId)->update(['email' => $email]);
    }
}

