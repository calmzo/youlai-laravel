<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Admin\BaseController;
use App\Inputs\Admin\System\ConfigFormInput;
use App\Inputs\Admin\System\ConfigPageInput;
use App\Services\System\ConfigService;

class ConfigController extends BaseController
{
    public $except = [];

    public function getConfigPage()
    {
        $input    = ConfigPageInput::new();
        $paginate = ConfigService::getInstance()->getConfigPage($input);
        return $this->successPaginate($paginate);
    }

    public function getConfigForm($id)
    {
        $list = ConfigService::getInstance()->getConfigForm($id);
        return $this->success($list);

    }

    /**
     * @permission('hasPermi','sys:config:add')
     * @logAnnotation('新增系统配置','SYSTEM_CONFIG')
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/8/2 11:26
     */
    public function saveConfig()
    {
        $input  = ConfigFormInput::new();
        $config = ConfigService::getInstance()->saveConfig($input);
        return $this->success($config);
    }


    /**
     * @permission('hasPermi','sys:config:update')
     * @logAnnotation('修改系统配置','SYSTEM_CONFIG')
     *
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/8/2 11:27
     */

    public function updateConfig($id)
    {
        $input  = ConfigFormInput::new();
        $config = ConfigService::getInstance()->updateConfig($id, $input);
        return $this->success($config);
    }


    /**
     * 删除配置
     * @permission('hasPermi','sys:config:delete')
     * @logAnnotation('删除系统配置','SYSTEM_CONFIG')
     * @param $ids
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/8/2 11:32
     */
    public function deleteConfig($ids)
    {
        $res = ConfigService::getInstance()->deleteConfig($ids);
        return $this->success($res);
    }

    /**
     * 刷新系统配置缓存
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/8/6 15:38
     */
    public function refreshCache()
    {
        $res = ConfigService::getInstance()->refreshCache();
        return $this->success($res);
    }

    /**
     * 获取系统配置
     *
     * @param $key
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/8/6 15:34
     */
    public function getSystemConfig($key)
    {
        $res = ConfigService::getInstance()->getSystemConfig($key);
        return $this->success($res);
    }

}
