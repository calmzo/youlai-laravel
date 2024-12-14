<?php

namespace App\Services\System;

use App\Inputs\Admin\System\ConfigFormInput;
use App\Inputs\Admin\System\ConfigPageInput;
use App\Models\System\SysConfig;
use App\Services\BaseService;
use App\Utils\CodeResponse;
use App\Utils\RedisCache;
use App\Utils\RedisConstant;

class ConfigService extends BaseService
{

    public function getConfigPage(ConfigPageInput $input, $columns = ['*'])
    {
        $columns  = ['id', 'config_name', 'config_key', 'config_value', 'remark'];
        $query    = SysConfig::query();
        $rolePage = $query
            ->when($input->keywords, function ($query, $keywords) {
                $query->where('config_key', 'like', "%{$keywords}%")->orWhere('config_name', 'like', "%{$keywords}%");
            })
            ->orderBy($input->sort, $input->order)
            ->paginate($input->pageSize, $columns, 'page', $input->pageNum);
        return $rolePage;
    }


    public function getConfigForm($id)
    {
        $columns  = ['id', 'config_name', 'config_key', 'config_value', 'remark'];
        $form   = SysConfig::query()->where('id', $id)->first($columns);
        if (is_null($form)) {
            //数据项不存在
            $this->throwBusinessException();
        }
        $form = $form->toArray();
        return $form;
    }

    public function saveConfig(ConfigFormInput $input)
    {
        $config         = SysConfig::new();
        $config->config_name  = $input->configName;
        $config->config_key  = $input->configKey;
        $config->config_value  = $input->configValue;
        $config->remark = $input->remark;
        $config->create_by = LoginService::getInstance()->userId();
        $result         = $config->save();
        return $result;
    }


    /**
     * 修改配置
     *
     * @param $id
     * @param ConfigFormInput $input
     * @return bool
     * @author 2024/8/2 11:30
     */
    public function updateConfig($id, ConfigFormInput $input)
    {
        $config         = SysConfig::query()->find($id);
        $count = SysConfig::query()->where('config_key', $input->configKey)->where('id', '<>', $id)->count();
        if ($count > 0) {
            $this->throwBusinessException(CodeResponse::SYSTEM_EXECUTION_ERROR, '配置键已存在');
        }
        $config->config_name  = $input->configName;
        $config->config_key  = $input->configKey;
        $config->config_value  = $input->configValue;
        $config->remark = $input->remark;
        $config->update_by = LoginService::getInstance()->userId();
        $result         = $config->save();
        return $result;
    }


    /**
     * 删除配置
     *
     * @param $idsStr
     * @return bool|int|mixed|null
     * @throws \App\Exceptions\BusinessException
     * @author 2024/8/2 11:31
     */
    public function deleteConfig($idsStr)
    {
        if (!$idsStr) {
            //删除数据为空
            $this->throwBusinessException();
        }
        $ids    = explode(',', $idsStr);
        $result = SysConfig::query()->whereIn('id', $ids)->delete();
        return $result;
    }


    /**
     * 刷新系统配置缓存
     *
     * @return bool
     * @author 2024/8/6 15:38
     */
    public function refreshCache()
    {
        RedisCache::getInstance()->delete(RedisConstant::SYSTEM_CONFIG_KEY);
        $map = SysConfig::query()->pluck('config_value', 'config_key')->toArray();
        if ($map) {
            RedisCache::getInstance()->hMSet(RedisConstant::SYSTEM_CONFIG_KEY, $map);
            return true;
        }

        return false;
    }

    /**
     * 获取系统配置
     *
     * @param $key
     * @return array|mixed|null
     * @author 2024/8/6 15:34
     */
    public function getSysConfig($key)
    {
        if ($key) {
            return RedisCache::getInstance()->hGet(RedisConstant::SYSTEM_CONFIG_KEY, $key);
        }
        return null;
    }


}
