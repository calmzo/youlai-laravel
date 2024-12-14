<?php

namespace App\Services\System;

use App\Inputs\Admin\System\DictFormInput;
use App\Inputs\Admin\System\DictPageInput;
use App\Models\System\SysDict;
use App\Models\System\SysDictItem;
use App\Services\BaseService;
use App\Tools\Helpers;
use App\Utils\CodeResponse;
use App\Utils\Constant;

class DictService extends BaseService
{


    /**
     * 字典分页列表
     *
     * @param DictPageInput $input
     * @param string[] $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @author 2024/6/20 17:09
     */
    public function getDictPage(DictPageInput $input, $columns = ['*'])
    {
        $columns  = ['id', 'dict_code', 'name', 'status', 'remark'];
        $keywords = $input->keywords;
        $page     = SysDict::query()
            ->when($keywords, function ($query, $keywords) {
                $query->where('name', 'like', "%{$keywords}%");
            })
            ->orderBy($input->sort, $input->order)
            ->paginate($input->pageSize, $columns, 'page', $input->pageNum);
        return $page;
    }


    public function getAllDictWithData()
    {
        $column = ['name', 'dict_code'];
        $list = SysDict::query()->with(['dictDataList' => function ($query) {
            $query->select(['dict_code', 'value', 'label', 'tag_type', 'status', 'sort', 'remark']);
        }])->get($column);
        return $list;
    }


    /**
     * 字典数据项表单详情
     *
     * @param $id
     * @return array
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 17:14
     */
    public function getDictForm($id)
    {
        $column = ['id', 'dict_code', 'name', 'status', 'remark'];
        $form   = SysDict::query()
            ->where('id', $id)
            ->first($column);
        if (is_null($form)) {
            //字典数据项不存在
            $this->throwBusinessException(CodeResponse::RESOURCE_NOT_FOUND);
        }
        return $form;
    }


    /**
     * 新增字典数据项
     *
     * @param DictFormInput $input
     * @return bool
     * @author 2024/6/20 17:21
     */
    public function saveDict(DictFormInput $input)
    {
        $count = SysDict::query()->where('dict_code', $input->dictCode)->count();
        if ($count > 0) {
            //字典数据项已存在
            $this->throwBusinessException(CodeResponse::RESOURCE_EXIST);
        }
        $dict         = SysDict::new();
        $dict->name   = $input->name;
        $dict->dict_code   = $input->dictCode;
        $dict->status = $input->status;
        $dict->remark = $input->remark;
        $result       = $dict->save();
        return $result;
    }


    /**
     * 修改字典数据项
     *
     * @param $id
     * @param DictFormInput $input
     * @return bool
     * @author 2024/6/20 17:21
     */
    public function updateDict($id, DictFormInput $input)
    {

        $count = SysDict::query()->where('dict_code', $input->dictCode)->where('id', '<>', $id)->count();
        if ($count > 0) {
            //字典数据项已存在
            $this->throwBusinessException(CodeResponse::RESOURCE_EXIST, '字典编码已存在');
        }

        $dict            = SysDict::query()->find($id);
        $dict->name   = $input->name;
        $dict->dict_code   = $input->dictCode;
        $dict->status = $input->status;
        $dict->remark = $input->remark;
        $result          = $dict->save();

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
    public function deleteDict($idsStr)
    {
        if (!$idsStr) {
            //删除数据为空
            $this->throwBusinessException(CodeResponse::PARAM_IS_NULL, '请选择需要删除的字典');
        }
        $ids    = explode(',', $idsStr);
        foreach ($ids as $id) {
            $result = SysDict::query()->where('id', $id)->delete();
        }


        return $result;
    }

}
