<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Admin\BaseController;
use App\Inputs\Admin\System\DictDataFormInput;
use App\Inputs\Admin\System\DictDataPageInput;
use App\Services\System\DictDataService;

class DictDataController extends BaseController
{
    public $except = [];


    /**
     * 字典分页列表
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 17:08
     */
    public function getDictDataPage()
    {
        $input = DictDataPageInput::new();
        $paginate = DictDataService::getInstance()->getDictDataPage($input);
        return $this->successPaginate($paginate);
    }


    /**
     * 字典数据表单数据
     *
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 17:14
     */
    public function getDictDataForm($id)
    {
        $list = DictDataService::getInstance()->getDictDataForm($id);
        return $this->success($list);

    }

    /**
     * 新增字典
     * @permission('hasPermi','sys:dict-data:add')
     * @logAnnotation('新增系统字典','SYSTEM_DICT')
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 17:23
     */
    public function saveDictData()
    {
        $input = DictDataFormInput::new();
        $dict = DictDataService::getInstance()->saveDictData($input);
        return $this->success($dict);
    }


    /**
     * 修改字典
     * @permission('hasPermi','sys:dict-data:edit')
     * @logAnnotation('修改系统字典','SYSTEM_DICT')
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 17:24
     */
    public function updateDictData($id)
    {
        $input = DictDataFormInput::new();
        $dict = DictDataService::getInstance()->updateDictData($id, $input);
        return $this->success($dict);
    }


    /**
     * 删除字典
     * @permission('hasPermi','sys:dict-data:delete')
     * @logAnnotation('删除系统字典','SYSTEM_DICT')
     * @param $ids 字典ID，多个以英文逗号(,)拼接"
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 17:25
     */
    public function deleteDictData($ids)
    {
        $res = DictDataService::getInstance()->deleteDictData($ids);
        return $this->success($res);
    }



    /**
     * 获取字典下拉列表
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/6/20 16:35
     */
    public function getDictDataList($dictCode)
    {
        $list = DictDataService::getInstance()->getDictDataList($dictCode);
        return $this->success($list);
    }


    /**
     * 字典类型分页列表
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 17:08
     */
    public function getDictTypePage()
    {
        $input = DictTypePageInput::new();
        $paginate = DictTypeService::getInstance()->getDictTypePage($input);
        return $this->successPaginate($paginate);
    }


    /**
     * 获取字典类型表单详情
     *
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 18:49
     */
    public function getDictTypeForm($id)
    {
        $list = DictTypeService::getInstance()->getDictTypeForm($id);
        return $this->success($list);

    }


    /**
     * 新增字典类型
     * @permission('hasPermi','sys:dict_type:add')
     * @logAnnotation('新增系统字典类型','SYSTEM_DICT')
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 17:23
     */
    public function saveDictType()
    {
        $input = DictTypeFormInput::new();
        $dictType = DictTypeService::getInstance()->saveDictType($input);
        return $this->success($dictType);
    }


    /**
     * 修改字典类型
     * @permission('hasPermi','sys:dict_type:edit')
     * @logAnnotation('修改系统字典类型','SYSTEM_DICT')
     *
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 17:24
     */
    public function updateDictType($id)
    {
        $input = DictTypeFormInput::new();
        $dictType = DictTypeService::getInstance()->updateDictType($id, $input);
        return $this->success($dictType);
    }


    /**
     * 删除字典类型
     * @permission('hasPermi','sys:dict_type:delete')
     * @logAnnotation('删除系统字典类型','SYSTEM_DICT')
     * @param $ids
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 18:00
     */
    public function deleteDictTypes($ids)
    {
        $res = DictTypeService::getInstance()->deleteDictTypes($ids);
        return $this->success($res);
    }

}
