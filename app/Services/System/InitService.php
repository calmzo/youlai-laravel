<?php

namespace App\Services\System;

use App\Enums\UserListEnum;
use App\Models\Channel;
use App\Models\Course;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\System\AdminUser;
use App\Models\System\SysUser;
use App\Models\Thread;
use App\Models\UserList;
use App\Models\UserProfile;
use App\Services\BaseService;
use App\Tools\Helpers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class InitService extends BaseService
{

    public function initUser()
    {
        $oldUserList = AdminUser::query()->get();
        foreach ($oldUserList as $oldUser) {
            $user = SysUser::query()->where('username', $oldUser->username)->first();
            if ($user) {
                $user->username = $oldUser->username ?? '';
                $user->nickname = $oldUser->nickname ?? '';
                $user->gender   = $oldUser->gender ?? 1;
                $user->avatar   = $oldUser->avatar ?: 'http://cdnwm.yuluojishu.com/c5ca2fb3e92a726d4dec641fc87c2f98.jpg';
                $user->dept_id  = $this->deptIdOldToNew($oldUser->department_id);
                $res            = $user->save();
                if ($res) {
                    $roleIds = [3];
                    // 保存用户角色(更新关联)
                    $user->roles()->sync($roleIds);
                    Log::info(sprintf('编辑用户：%s，权限ID：【%s】', $user->username, json_encode($roleIds)));
                }
            } else {
                $user           = SysUser::new();
                $user->username = $oldUser->username ?? '';
                $user->nickname = $oldUser->nickname ?? '';
                $user->gender   = $oldUser->gender ?? 1;
                $user->avatar   = $oldUser->avatar ?: 'http://cdnwm.yuluojishu.com/c5ca2fb3e92a726d4dec641fc87c2f98.jpg';
                $user->password = Hash::make('123456');
                $user->status   = 1;
                $user->dept_id  = $this->deptIdOldToNew($oldUser->department_id);
                $user->mobile   = '';
                $user->email    = '';
                $res            = $user->save();
                //权限
                if ($res) {
                    $roleIds = [3];
                    $user->roles()->attach($roleIds);
                    Log::info(sprintf('初始化用户：%s，权限ID：【%s】', $user->username, json_encode($roleIds)));
                }
            }

        }
        return true;
    }

    public function deptIdOldToNew($oldDeptId)
    {
        $deptMap = [
            '1'  => 2,
            '2'  => 3,
            '3'  => 4,
            '4'  => 5,
            '5'  => 6,
            '6'  => 7,
            '7'  => 1,
            '8'  => 8,
            '9'  => 9,
            '10' => 10,
            '11' => 11,
            '12' => 12,
        ];

        return $deptMap[$oldDeptId] ?? 0;
    }


    public function initThread($count, $createDate, $merchantId, $customerId)
    {
        $maxId = Thread::query()->orderByDesc('id')->limit(1)->value('id');
        $this->addThread($count, $maxId, $createDate, $merchantId, $customerId);
        return true;
    }

    public function addThread($count, $maxId, $date, $merchantId, $customerId)
    {

//        $createTime = strtotime($date.date('H:i:s'));
        $channelId = 779;
        $createTime = strtotime($date);
        for ($i = 1; $i <= $count; $i++) {
            $maxId        += $i;
            $provinceCity = Helpers::randomProvinceCity();
            $user         = $this->saveUser([
                'username'   => '测试公海线索' . $maxId,
                'phone'      => Helpers::randomPhoneNumber(),
                'channelId'  => $channelId,
                'province'   => $provinceCity['province'] ?? '',
                'city'       => $provinceCity['city'] ?? '',
                'createTime' => $createTime
            ]);
            if ($user) {
                $userProfile = UserProfile::new();
                $userProfile->uid = $user->id;
                $userProfile->save();
                //插入线索表
                $thread = $this->saveThread([
                    'userId'     => $user->id,
                    'merchantId' => $merchantId,
                    'customerId' => $customerId,
                    'createTime' => $createTime,
                ]);
                Log::channel('thread')->info(sprintf('手动添加线索成功：%s，用户ID：%s', $thread->id, $user->id));
            }
        }


    }

    private function saveUser($data = [])
    {
        $phone      = $data['phone'] ?? '';
        $channelId  = $data['channelId'] ?? '';
        $username   = $data['username'] ?? '';
        $province   = $data['province'] ?? '';
        $city       = $data['city'] ?? '';
        $createTime = $data['createTime'] ?? '';
        $is_test = 0;
//        if (strpos($username, '测试') !== false || substr($phone, 0, 2) === '11') {
//            $is_test = 1;
//        }

        $channelInfo = Channel::query()->find($channelId);
        $app         = $channelInfo->app;
        if (empty($channelInfo) || empty($app)) {
            return false;
        }
        $user = UserList::query()->where('phone', $phone)->first();
        if (!$user) {
            $user = UserList::new();
        }
        $user->phone            = $phone ?? '';
        $user->phone_end_number = !empty($phone) ? substr($phone, -4) : '';
        $user->wx_number        = $data['weixin'] ?? '';
        $user->nickname         = $username;
        $user->channel          = $channelInfo->channel_name;
        $user->channel_id       = $channelInfo->id ?? 0;
        $user->app_id           = $channelInfo->app_id ?? 0;
        $user->app_class_id     = $app->app_class_id ?? 0;
        $user->province         = $province;
        $user->city             = $city;
        $user->age_range_id     = 2;
        $user->sex              = $data['sex'] ?? 0;
        $user->login_time       = date('Y-m-d H:i:s');
        $user->is_wechat        = 1;
        $user->source           = UserListEnum::SOURCE_NEW_MEDIA;
        $user->is_test          = $is_test;
        $user->create_time      = $createTime;
        $user->save();
        return $user;
    }

    public function saveThread($data)
    {
        $userId       = $data['userId'];
        $merchantId   = $data['merchantId'];
        $customerId   = $data['customerId'];
        $createTime   = $data['createTime'] ?? '';
        $merchantInfo = Merchant::query()->find($merchantId);
        if (empty($merchantInfo) || ($merchantInfo->is_switch == 0)) {
            return false;
        }
//        $courseId = Course::query()->where('merchant_id', $merchantId)->value('id');
//        if (empty($courseId)) {
//            $courseId = 0;
//        }
        $userInfo    = UserList::query()->where('id', $userId)->first();
        $channelInfo = Channel::query()->where('channel_name', $userInfo->channel)->first();
        $app         = $channelInfo->app;
        if (empty($channelInfo)) {
            Log::channel('thread')->error('飞鱼crm导入线索失败-渠道信息有误');
            return false;
        }
        if ($customerId) {
            $customer = Customer::query()->find($customerId);
        } else {
            //商户下随机客服
            $customer = Customer::query()->where('merchant_id', $merchantId)->where('')->inRandomOrder()->first();
        }
        $customerId = $customer->id ?? 0;
        $merchantId = $customer->merchant_id ?? 0;

        $thread = Thread::new();
        $thread->uid = $userInfo->id;
        $thread->course_id = $courseId ?? 0;
        $thread->merchant_id = $merchantId ?? '';
        $thread->customer_id = $customerId ?? '';
        $thread->province = $userInfo->province ?? '';
        $thread->city = $userInfo->city ?? '';
        $thread->age = 0;
        $thread->channel = $userInfo->channel ?? '';
        $thread->channel_id = $channelInfo->id ?? '';
        $thread->app_id = $channelInfo->app_id ?? '';
        $thread->app_class_id = $app->app_class_id ?? '';
        $thread->thread_type = isset($merchantInfo->is_jump_miniprogram) ? ($merchantInfo->is_jump_miniprogram > 0 ? 3 : 1) : 0;
        $thread->source = $channelInfo->source_id ?? '';
        $thread->is_free_try = $merchantInfo->is_free_try ?? '';
        $thread->is_test = $userInfo->is_test ?? '';
        $thread->age_id = $userInfo->age_range_id ?? '';
        $thread->merchant_admin_id = $merchantInfo->admin_ids ?? '';
        $thread->is_origin = 4;
        $thread->source_id = 4;
        $thread->create_time = $createTime ?? time();
        $thread->save();
        return $thread;
    }



    public function initOverdueUser($count, $createDate)
    {
        $createTime = strtotime($createDate);
        $channelId  = 768;
        $channelInfo = Channel::query()->find($channelId);
        $app         = $channelInfo->app;
        if (empty($channelInfo) || empty($app)) {
            return false;
        }
        for ($i = 1; $i <= $count; $i++) {
            $phone      = Helpers::randomPhoneNumber();
            $maxId = UserList::query()->orderByDesc('id')->limit(1)->value('id');
            $username   = '测试用户' . $maxId;
            $province   = '';
            $city       = '';
            $user = UserList::query()->where('phone', $phone)->first();
            if (!$user) {
                $user = UserList::new();
            }
            $user->phone            = $phone ?? '';
            $user->phone_end_number = !empty($phone) ? substr($phone, -4) : '';
            $user->wx_number        = $data['weixin'] ?? '';
            $user->nickname         = $username;
            $user->channel          = $channelInfo->channel_name;
            $user->channel_id       = $channelInfo->id ?? 0;
            $user->app_id           = $channelInfo->app_id ?? 0;
            $user->app_class_id     = $app->app_class_id ?? 0;
            $user->province         = $province;
            $user->city             = $city;
            $user->age_range_id     = 2;
            $user->sex              = 0;
            $user->login_time       = date('Y-m-d H:i:s');
            $user->is_wechat        = 1;
            $user->source           = UserListEnum::SOURCE_NEW_MEDIA;
            $user->create_time      = $createTime;
            $user->save();
        }
        return true;
    }

}
