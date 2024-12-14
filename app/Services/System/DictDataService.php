<?php

namespace App\Services\System;

use App\Inputs\Admin\System\DictDataFormInput;
use App\Inputs\Admin\System\DictDataPageInput;
use App\Models\System\SysDict;
use App\Models\System\SysDictData;
use App\Services\BaseService;
use App\Tools\Helpers;
use App\Utils\CodeResponse;
use App\Utils\Constant;

class DictDataService extends BaseService
{


    /**
     * 字典数据分页列表
     *
     * @param DictDataPageInput $input
     * @param string[] $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @author 2024/6/20 17:09
     */
    public function getDictDataPage(DictDataPageInput $input, $columns = ['*'])
    {
        $columns  = ['id', 'dict_code', 'value', 'label', 'tag_type', 'status', 'sort', 'remark'];
        $dictCode = $input->dictCode;
        $page     = SysDictData::query()
            ->when($dictCode, function ($query, $dictCode) {
                $query->where('dict_code', $dictCode);
            })
            ->orderBy($input->sort, $input->order)
            ->paginate($input->pageSize, $columns, 'page', $input->pageNum);
        return $page;
    }


    public function listDictOptions($code)
    {
        $dictData = SysDictData::query()->where('dict_code', $code)->where('status', 1)->get(['label', 'value']);
//        $options = Helpers::model2Options($dictData, 'value');
        return $dictData;
    }


    /**
     * 字典数据项表单详情
     *
     * @param $id
     * @return array
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 17:14
     */
    public function getDictDataForm($id)
    {
        $columns  = ['id', 'dict_code', 'value', 'label', 'tag_type', 'status', 'sort', 'remark'];
        $form   = SysDictData::query()
            ->where('id', $id)
            ->first($columns);
        if (is_null($form)) {
            //字典数据项不存在
            $this->throwBusinessException(CodeResponse::RESOURCE_NOT_FOUND);
        }
        return $form;
    }


    /**
     * 新增字典数据项
     *
     * @param DictDataFormInput $input
     * @return bool
     * @author 2024/6/20 17:21
     */
    public function saveDictData(DictDataFormInput $input)
    {
        $dictData         = SysDictData::new();
        $dictData->label   = $input->label;
        $dictData->value = $input->value;
        $dictData->dict_code   = $input->dictCode;
        $dictData->sort = $input->sort;
        $dictData->status = $input->status;
        $dictData->tag_type = $input->tagType;
        $result       = $dictData->save();
        return $result;
    }


    /**
     * 修改字典数据项
     *
     * @param $id
     * @param DictDataFormInput $input
     * @return bool
     * @author 2024/6/20 17:21
     */
    public function updateDictData($id, DictDataFormInput $input)
    {

        $dictData            = SysDictData::query()->find($id);
        $dictData->label   = $input->label;
        $dictData->value = $input->value;
        $dictData->dict_code   = $input->dictCode;
        $dictData->sort = $input->sort;
        $dictData->status = $input->status;
        $dictData->tag_type = $input->tagType;
        $result          = $dictData->save();

        return $result;
    }


    /**
     * 删除字典数据项
     *
     * @param $idsStr
     * @return mixed
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 17:23
     */
    public function deleteDictData($idsStr)
    {
        if (!$idsStr) {
            //删除数据为空
            $this->throwBusinessException(CodeResponse::PARAM_IS_NULL, '请选择需要删除的字典');
        }
        $ids    = explode(',', $idsStr);
        foreach ($ids as $id) {
            $result = SysDictData::query()->where('id', $id)->delete();
        }


        return $result;
    }

}
