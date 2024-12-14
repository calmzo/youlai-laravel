<?php

namespace App\Services\System;

use App\Enums\MenuEnum;
use App\Inputs\Admin\System\MenuListInput;
use App\Inputs\Admin\System\MenuSaveInput;
use App\Models\System\SysMenu as Menu;
use App\Services\BaseService;
use App\Utils\Constant;
use App\Utils\RedisCache;
use Illuminate\Support\Str;

class MenuService extends BaseService
{
    /**
     * 获取菜单下拉列表
     *
     * @return array
     * @author 2024/6/11 10:28
     */
    public function listMenuOptions()
    {
        $menuList = Menu::query()->orderBy('sort')->get();
        return $this->buildMenuOptions($menuList);
    }

    public function listMenus(MenuListInput $input)
    {

        $query    = Menu::query();
        $query    = $this->getMenuQuery($query, $input);
        $menuList = $query->get();
        $menuList = $menuList->each(function ($item) {
            $item->type = MenuEnum::typeDescMap($item->type);
        });
        $menuList = $menuList->toArray();
        $res      = $this->buildMenuTree($menuList);

        return $res;
    }


    public static function buildMenuTree($data, $parent_id = 0)
    {
        $tree = [];
        foreach ($data as $k => $v) {
            // 一、根据传入的某个父节点ID,遍历该父节点的所有子节点
            if ($v['parentId'] == $parent_id) {
                $v['children'] = self::buildMenuTree($data, $v['id']);
                $tree[]        = $v;
                unset($data[$k]);
            }
        }
        return $tree;
    }

    private function getMenuQuery($query, MenuListInput $input)
    {
        if (!empty($input->keywords)) {
            $query->where('name', 'like', "%{$input->keywords}%");
        }

        return $query;
    }

    /**
     * 获取路由列表
     *
     * @param null $roles
     * @return array|false|mixed|null
     * @author 2024/8/1 18:23
     */
    public function listRoutes($roles = null)
    {
        if (empty($roles)) {
            return [];
        }
        $menuList = Menu::query()
            ->with('roles')
            ->when($roles, function ($query) use ($roles) {
                // ROOT 可查看所有菜单
                $query->when(!in_array('ROOT', $roles), function ($query) use ($roles) {
                    $query->whereHasIn('roles', function ($query) use ($roles) {
                        $query->whereIn('code', $roles);
                    });
                });

            })
            ->where('type', '<>', MenuEnum::TYPE_BUTTON)
            ->orderBy('sort')
            ->get();

        $routes = $this->buildRoutes($menuList);

        return $routes;
    }


    /**
     * 递归生成菜单路由层级列表
     *
     * @param $menuList
     * @param int $parentId
     * @return mixed
     * @author 2024/6/13 16:16
     */
    public function buildRoutes($menuList, $parentId = 0)
    {
        $routeList = [];
        foreach ($menuList as $menu) {
            if ($menu->parent_id == $parentId) {

                // 路由参数字符串 {"id":"1","name":"张三"} 转换为 [{key:"id", value:"1"}, {key:"name", value:"张三"}]
                $params = $menu->params;
                if ($params) {
                    $params = array_column($params, 'value', 'key');
                }
                $routeName = $menu->route_name ?: $this->toCamelCase($menu->route_path);

                $meta = [
                    'title'      => $menu->name,
                    'icon'       => $menu->icon,
                    'hidden'     => $menu->visible == 0,
//                    'roles'      => $menu->roles->pluck('code')->toArray(),
                    'params'     => $params,
                    'alwaysShow' => $menu->always_show == 1,
                ];
                if ($menu->type == 1 && $menu->keep_alive == 1) {
                    $meta['keepAlive'] = true;
                }
                $routeInfo = [
                    'name'      => $routeName,
                    'path'      => $menu->route_path,
                    'component' => $menu->component,
                    'redirect'  => $menu->redirect,
                    'meta'      => $meta,
                ];
                $children  = $this->buildRoutes($menuList, $menu->id);
                if (!empty($children)) {
                    $routeInfo['children'] = $children;
                }
                $routeList[] = $routeInfo;
            }
        }
        return $routeList;
    }

    /**
     * 下划线转驼峰
     *
     * @param $string
     * @return string
     * @author 2024/6/13 18:07
     */
    private function toCamelCase($string)
    {
        $camelCaseString = Str::camel($string);
        $camelCaseString = ucfirst($camelCaseString); // 输出 'HelloWorld'

        return $camelCaseString;
    }

    /**
     * 递归生成菜单下拉层级列表
     *
     * @param $menuList
     * @param int $parentId
     * @return array
     * @author 2024/6/11 10:28
     */
    public function buildMenuOptions($menuList, $parentId = 0)
    {
        $menuOptions = [];
        foreach ($menuList as $menu) {
            if ($menu->parent_id == $parentId) {
                $option = [
                    'value' => $menu->id,
                    'label' => $menu->name
                ];

                $subMenuOptions = $this->buildMenuOptions($menuList, $menu->id);
                if (!empty($subMenuOptions)) {
                    $option['children'] = $subMenuOptions;
                }
                $menuOptions[] = $option;
            }
        }
        return $menuOptions;

    }

    /**
     * 获取菜单表单数据
     *
     * @param $id
     * @return array
     * @throws \App\Exceptions\BusinessException
     * @author 2024/6/18 17:16
     */
    public function getMenuForm($id)
    {
        $column = ['id', 'name', 'parent_id', 'sort', 'type', 'route_name', 'route_path', 'component', 'perm', 'visible', 'icon', 'redirect', 'tree_path', 'keep_alive', 'always_show', 'params'];
        $form   = Menu::query()->where('id', $id)->first($column);
        if (is_null($form)) {
            //菜单不存在
            $this->throwBusinessException();
        }
        // 路由参数字符串 {"id":"1","name":"张三"} 转换为 [{key:"id", value:"1"}, {key:"name", value:"张三"}]
        $params = $form->params;
        if ($params) {
            // 转换为 List<KeyValue> 格式 [{key:"id", value:"1"}, {key:"name", value:"张三"}]
            $params = array_map(function ($key, $value) {
                $value = array_values($value);
                return ['key' => $value[0] ?? [], 'value' => $value[1] ?? []];
            }, array_keys($params), array_values($params));
        }
        $form->params = $params;
        $form->type   = MenuEnum::typeDescMap($form->type);

//        $form = $form->toArray();
        return $form;
    }

    public function saveMenu(MenuSaveInput $input)
    {
        $menuType = $input->type;
        if ($menuType == 'CATALOG') {  // 如果是目录
            if ($input->parentId == 0 && !(strpos($input->routePath, '/') === 0)) {
//                $path        = "/" . $path;// 一级目录需以 / 开头
                $input->routePath = "/" . $input->routePath;// 一级目录需以 / 开头
            }
            $input->component = 'Layout';
        } else if ($menuType == 'EXTLINK') {   // 如果是外链
//            $input->component = null;
        }
        $treePath = $this->generateMenuTreePath($input->parentId);
        $params   = $input->params;
        // 路由参数 [{key:"id",value:"1"}，{key:"name",value:"张三"}] 转换为 [{"id":"1"},{"name":"张三"}]
        if ($params) {
            $params = array_map(function ($key, $value) {
                return [$key => $value];
            }, array_column($params, 'key'), array_column($params, 'value'));
        } else {
            $params = [];
        }
        if ($input->id) {
            $menu = Menu::query()->where('id', $input->id)->first();
        } else {
            $menu = Menu::new();
        }

        $menu->name        = $input->name;
        $menu->parent_id   = $input->parentId;
        $menu->type        = MenuEnum::typeMap($menuType);
        $menu->route_name  = $input->routeName;
        $menu->route_path  = $input->routePath;
        $menu->component   = $input->component;
        $menu->perm        = $input->perm;
        $menu->visible     = $input->visible;
        $menu->sort        = $input->sort;
        $menu->icon        = $input->icon;
        $menu->redirect    = $input->redirect;
        $menu->keep_alive  = $input->keepAlive;//页面缓存
        $menu->always_show = $input->alwaysShow;
        $menu->params      = $input->params;
        $menu->tree_path   = $treePath;
//        $menu->params      = json_encode($params);
        if ($input->component) {
            $menu->component = $input->component;
        }

        $result = $menu->save();
        if (!$result) {
            //菜单保存失败
            $this->throwBusinessException();
        }
        // 编辑刷新角色权限缓存
        RoleMenuService::getInstance()->refreshRolePermsCache();
        // 修改菜单如果有子菜单，则更新子菜单的树路径
        $this->updateChildrenTreePath($menu->id, $treePath);
        return $menu;
    }

    /**
     * 更新子菜单树路径
     *
     * @param $id 当前菜单ID
     * @param $treePath 当前菜单树路径
     * @return void
     * @author 2024/9/30 17:24
     */
    public function updateChildrenTreePath($id, $treePath)
    {
        $children = Menu::query()->where('parent_id', $id)->get();
        if ($children) {
            // 子菜单的树路径等于父菜单的树路径加上父菜单ID
            $childTreePath = $treePath . "," . $id;
            Menu::query()->where('parent_id', $id)->update(['tree_path' => $childTreePath]);
            foreach ($children as $child) {
                // 递归更新子菜单
                $this->updateChildrenTreePath($child->id, $childTreePath);
            }
        }
    }

    /**
     * 删除菜单
     *
     * @param $id
     * @return bool
     * @throws \Exception
     * @author 2024/6/18 16:50
     */
    public function deleteMenu($id)
    {
        $res = Menu::query()
            ->where(function ($query) use ($id) {
                $query->where('id', $id)->orWhereRaw("CONCAT (',',tree_path,',') LIKE CONCAT('%,',{$id},',%')");
            })
            ->delete();
        if ($res) {
            //刷新角色权限缓存
            RoleMenuService::getInstance()->refreshRolePermsCache();
        }
        return $res;
    }


    /**
     * 菜单路径生成
     *
     * @param $parentId
     * @return string|null
     * @author 2024/6/19 13:43
     */
    private function generateMenuTreePath($parentId)
    {
        if ($parentId == 0) {
            return $parentId;
        } else {
            $parentMenu = Menu::query()->where('id', $parentId)->first();
            return !is_null($parentMenu) ? $parentMenu->tree_path . "," . $parentMenu->id : null;
        }
    }


    /**
     * 修改菜单显示状态
     *
     * @param $menuId 菜单ID
     * @param $visible 是否显示(1->显示；2->隐藏)
     * @return int
     * @author 2024/6/19 13:45
     */
    public function updateMenuVisible($menuId, $visible)
    {
        $res = Menu::query()
            ->where('id', $menuId)
            ->update(['visible' => $visible]);
        return $res;
    }

}
