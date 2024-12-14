<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Admin\BaseController;
use App\Inputs\Admin\System\DeptInput;
use App\Inputs\Admin\System\DeptSaveInput;
use App\Services\System\DeptService;

class DeptController extends BaseController
{
    public $except = [];

    /**
     * 获取部门列表
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/18 11:40
     */
    public function listDepartments()
    {
        $input    = DeptInput::new();
        $list = DeptService::getInstance()->listDepartments($input);
        return $this->success($list);

    }


    /**
     * 获取部门下拉选项
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/6/18 11:58
     */
    public function listDeptOptions()
    {
        $list = DeptService::getInstance()->listDeptOptions();
        return $this->success($list);

    }

    /**
     * 获取部门详情
     * @param $deptId
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/6/18 13:34
     */
    public function getDeptForm($deptId)
    {
        $list = DeptService::getInstance()->getDeptForm($deptId);
        return $this->success($list);

    }

    /**
     * 新增部门
     * @permission('hasPermi','sys:dept:add')
     * @logAnnotation('新增系统部门','SYSTEM_DEPT')
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 11:57
     */
    public function saveDept()
    {
        $input = DeptSaveInput::new();
        $dept = DeptService::getInstance()->saveDept($input);
        return $this->success($dept);
    }


    /**
     * 修改部门
     * @permission('hasPermi','sys:dept:edit')
     * @logAnnotation('修改了系统部门','SYSTEM_DEPT')
     * @param $deptId
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/20 11:57
     */
    public function updateDept($deptId)
    {
        $input = DeptSaveInput::new();
        $dept = DeptService::getInstance()->updateDept($deptId, $input);
        return $this->success($dept);
    }

    /**
     * 删除部门
     * @permission('hasPermi','sys:dept:delete')
     * @logAnnotation('删除了系统部门','SYSTEM_DEPT')
     * @param $deptIds
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \Exception
     * @author 2024/6/20 11:58
     */
    public function deleteDepartments($deptIds)
    {
        $res = DeptService::getInstance()->deleteByIds($deptIds);
        return $this->success($res);
    }
}
