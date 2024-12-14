<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Admin\BaseController;
use App\Inputs\Admin\System\DictFormInput;
use App\Inputs\Admin\System\DictPageInput;
use App\Inputs\Admin\System\DictTypeFormInput;
use App\Inputs\Admin\System\DictTypePageInput;
use App\Services\System\DictService;
use App\Services\System\DictTypeService;

class DictController extends BaseController
{
    public $except = [];


    /**
     * 字典分页列表
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 17:08
     */
    public function getDictPage()
    {
        $input = DictPageInput::new();
        $paginate = DictService::getInstance()->getDictPage($input);
        return $this->successPaginate($paginate);
    }

    public function getAllDictWithData()
    {
        $paginate = DictService::getInstance()->getAllDictWithData();
        return $this->success($paginate);
    }


    /**
     * 字典数据表单数据
     *
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 17:14
     */
    public function getDictForm($id)
    {
        $list = DictService::getInstance()->getDictForm($id);
        return $this->success($list);

    }

    /**
     * 新增字典
     * @permission('hasPermi','sys:dict:add')
     * @logAnnotation('新增系统字典','SYSTEM_DICT')
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 17:23
     */
    public function saveDict()
    {
        $input = DictFormInput::new();
        $dict = DictService::getInstance()->saveDict($input);
        return $this->success($dict);
    }


    /**
     * 修改字典
     * @permission('hasPermi','sys:dict:edit')
     * @logAnnotation('修改系统字典','SYSTEM_DICT')
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 17:24
     */
    public function updateDict($id)
    {
        $input = DictFormInput::new();
        $dict = DictService::getInstance()->updateDict($id, $input);
        return $this->success($dict);
    }


    /**
     * 删除字典
     * @permission('hasPermi','sys:dict:delete')
     * @logAnnotation('删除系统字典','SYSTEM_DICT')
     * @param $ids 字典ID，多个以英文逗号(,)拼接"
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 17:25
     */
    public function deleteDict($ids)
    {
        $res = DictService::getInstance()->deleteDict($ids);
        return $this->success($res);
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
