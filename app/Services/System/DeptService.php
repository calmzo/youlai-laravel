<?php

namespace App\Services\System;

use App\Inputs\Admin\System\DeptInput;
use App\Inputs\Admin\System\DeptSaveInput;
use App\Models\System\SysDept as Dept;
use App\Services\BaseService;

class DeptService extends BaseService
{

    /**
     * 获取部门列表
     *
     * @param DeptInput $input
     * @param string[] $columns
     * @return array
     * @author 2024/6/18 11:40
     */
    public function listDepartments(DeptInput $input, $columns = ['*'])
    {
        $columns  = ['id', 'name', 'parent_id', 'sort', 'status', 'create_time', 'update_time'];
        $query    = Dept::query();
        $query    = $this->getDeptQuery(
            $query,
            $input
        );
        $deptList = $query->orderBy('sort')->get($columns);
        // 获取根节点ID（递归的起点），即父节点ID中不包含在部门ID中的节点，注意这里不能拿顶级部门 O 作为根节点，因为部门筛选的时候 O 会被过滤掉
        $rootList = $deptList->filter(function ($item) {
            return $item['parent_id'] == 0;
        });
        if (!$rootList->isEmpty()) {
            $deptList = $deptList->toArray();
            $deptList = $this->buildDeptTree($deptList);
        } else {
            $deptList = $deptList->toArray();
        }
        return $deptList;
    }

    /**
     * 递归生成部门树形列表
     *
     * @param $data
     * @param int $parent_id
     * @return array
     * @author 2024/6/18 11:58
     */
    public static function buildDeptTree($data, $parent_id = 0)
    {
        $tree = [];
        foreach ($data as $k => $v) {
            // 一、根据传入的某个父节点ID,遍历该父节点的所有子节点
            if ($v['parentId'] == $parent_id) {
                $v['children'] = self::buildDeptTree($data, $v['id']);
                unset($data[$k]);
                $tree[] = $v;
            }
        }
        return $tree;
    }


    private function getDeptQuery($query, DeptInput $input)
    {
        if (!empty($input->keywords)) {
            $query->where('name', 'like', "%{$input->keywords}%");
        }

        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        return $query;
    }


    /**
     * 获取部门下拉选项
     *
     * @return array|array[]
     * @author 2024/6/18 11:58
     */
    public function listDeptOptions()
    {

        $columns  = ['id', 'name', 'parent_id'];
        $deptList = Dept::query()
            ->where('status', 1)
            ->get($columns);

        // 获取根节点ID（递归的起点），即父节点ID中不包含在部门ID中的节点，注意这里不能拿顶级部门 O 作为根节点，因为部门筛选的时候 O 会被过滤掉
        $rootList = $deptList->filter(function ($item) {
            return $item['parent_id'] == 0;
        });

        $deptList = $deptList->toArray();
        $deptList = array_map(function ($item) {
            return [
                'value'    => $item['id'],
                'label'    => $item['name'],
                'parentId' => $item['parentId'],
            ];
        }, $deptList);

        if (!$rootList->isEmpty()) {
            $deptList = $this->buildDeptOptionsTree($deptList);
        }
        return $deptList;

    }

    /**
     * 递归生成部门下拉树形列表
     *
     * @param $data
     * @param int $parent_id
     * @return array
     * @author 2024/6/18 11:58
     */
    public function buildDeptOptionsTree($data, $parent_id = 0)
    {
        $tree = [];
        foreach ($data as $k => $v) {
            // 一、根据传入的某个父节点ID,遍历该父节点的所有子节点
            if ($v['parentId'] == $parent_id) {
                $v['children'] = self::buildDeptOptionsTree($data, $v['value']);
                unset($data[$k]);
                $tree[] = $v;
            }
        }
        return $tree;
    }


    /**
     * 获取部门详情
     *
     * @param $deptId
     * @return \App\Models\BaseModel|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/18 13:34
     */
    public function getDeptForm($deptId)
    {
        $column = ['id', 'name', 'parent_id', 'sort', 'status'];
        $form   = Dept::query()->where('id', $deptId)->first($column);
        if (is_null($form)) {
            $this->throwBusinessException();
        }

        return $form;
    }

    public function saveDept(DeptSaveInput $input)
    {
        $parentId = $input->parentId;
        $name     = $input->name;
        $count    = Dept::query()->where('name', $name)->count();
        if ($count != 0) {
            //部门名称已存在
            $this->throwBusinessException();
        }
        $treePath = $this->generateDeptTreePath($input->parentId);

        $dept            = Dept::new();
        $dept->name      = $name;
        $dept->parent_id = $parentId;
        $dept->tree_path = $treePath;
        $dept->sort      = $input->sort ?? 0;
        $dept->status    = $input->status ?? 0;
        $dept->name      = $name;
        $dept->create_by = LoginService::getInstance()->userId();
        $dept->update_by = LoginService::getInstance()->userId();
        $result          = $dept->save();
        if (!$result) {
            //部门保存失败
            $this->throwBusinessException();
        }

        return $dept;
    }


    /**
     * 修改部门
     *
     * @param $deptId
     * @param DeptSaveInput $input
     * @return \Illuminate\Database\Eloquent\HigherOrderBuilderProxy|mixed
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/18 16:50
     */
    public function updateDept($deptId, DeptSaveInput $input)
    {
        $dept = Dept::query()->where('id', $deptId)->first();

        if (is_null($dept)) {
            //部门不存在
            $this->throwBusinessException();
        }
        $parentId = $input->parentId;
        $name     = $input->name;
        $count    = Dept::query()->where('id', '<>', $deptId)->where('name', $name)->count();
        if ($count != 0) {
            //部门名称已存在
            $this->throwBusinessException();
        }
        $treePath        = $this->generateDeptTreePath($parentId);
        $dept->name      = $name;
        $dept->parent_id = $parentId;
        $dept->tree_path = $treePath;
        $dept->sort      = $input->sort ?? 0;
        $dept->status    = $input->status ?? 0;
        $dept->name      = $name;
        $dept->update_by = LoginService::getInstance()->userId();
        $result          = $dept->save();
        if (!$result) {
            //部门保存失败
            $this->throwBusinessException();
        }

        return $dept;
    }


    /**
     * 部门路径生成
     *
     * @param $parentId
     * @return string|null 父节点路径以英文逗号(, )分割，eg: 1,2,3
     * @author 2024/6/18 14:24
     */
    private function generateDeptTreePath($parentId)
    {

        $treePath = null;
        if ($parentId == 0) {
            $treePath = $parentId;
        } else {
            $parentDept = Dept::query()->where('id', $parentId)->first();
            if (!is_null($parentDept)) {
                $treePath = $parentDept->tree_path . "," . $parentDept->id;
            }
        }
        return $treePath;

    }

    /**
     * 删除部门
     *
     * @param $deptIds
     * @return bool
     * @throws \Exception
     * @author 2024/6/18 16:50
     */
    public function deleteByIds($deptIds)
    {
        $menuIds = explode(',', $deptIds);
        foreach ($menuIds as $deptId) {
            Dept::query()
                ->where(function ($query) use ($deptId) {
                    $query->where('id', $deptId)->orWhereRaw("CONCAT (',',tree_path,',') LIKE CONCAT('%,',{$deptId},',%')");
                })
                ->delete();
        }
        return true;
    }
}
