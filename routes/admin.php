<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\System\AuthController;
use App\Http\Controllers\Admin\System\UserController as SystemUserController;
use App\Http\Controllers\Admin\System\MenuController;
use App\Http\Controllers\Admin\System\RoleController;
use App\Http\Controllers\Admin\System\DeptController;
use App\Http\Controllers\Admin\System\DictController;
use App\Http\Controllers\Admin\System\DictDataController;
use App\Http\Controllers\Admin\System\LogController;
use App\Http\Controllers\Admin\System\ConfigController;
use App\Http\Controllers\Admin\System\NoticeController;
use App\Http\Controllers\FileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// 系统管理
Route::prefix('system')->group(function () {
    //用户模块
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::delete('logout', [AuthController::class, 'logout']); //注销
        Route::get('captcha', [AuthController::class, 'getCaptcha']); //获取验证码
        Route::post('refresh-token', [AuthController::class, 'refreshToken']); //刷新token
    });

    //用户管理
    Route::prefix('users')->group(function () {
        Route::get('me', [SystemUserController::class, 'getCurrentUserInfo']);
        Route::get('page', [SystemUserController::class, 'listPagedUsers']); //用户列表
        Route::post('', [SystemUserController::class, 'saveUser']);
        Route::get('profile', [SystemUserController::class, 'getUserProfile']); //获取个人中心用户信息
        Route::put('profile', [SystemUserController::class, 'updateUserProfile']); //修改个人中心用户信息
        Route::put('password', [SystemUserController::class, 'changePassword']); //修改用户密码
        Route::post('import', [SystemUserController::class, 'importUsers']); //导入用户
        Route::put('email', [SystemUserController::class, 'bindEmail']); //个人中心绑定用户邮箱
        Route::put('{userId}', [SystemUserController::class, 'updateUser']);
        Route::patch('{userId}/password', [SystemUserController::class, 'updatePassword']); //修改用户密码 //todo 准备弃用
        Route::put('{userId}/password/reset', [SystemUserController::class, 'resetPassword']); //重置用户密码
        Route::get('{userId}/form', [SystemUserController::class, 'getUserFormData']); //用户表单数据
        Route::delete('{ids}', [SystemUserController::class, 'deleteUsers']); //删除用户ID，多个以英文逗号(,)分割
        Route::get('template', [SystemUserController::class, 'downloadTemplate']); //模板
        Route::get('export', [SystemUserController::class, 'exportUsers']); //导出用户
        Route::get('options', [SystemUserController::class, 'listUserOptions']); //角色下拉列表

        Route::prefix('recycle')->group(function () {
            Route::get('page', [SystemUserController::class, 'listPagedRecycleUsers']); //回收站用户列表
            Route::patch('{ids}/restore', [SystemUserController::class, 'restoreRecycleUser']); //还原回收站用户
            Route::delete('{ids}', [SystemUserController::class, 'deleteRecycleUsers']); //删除回收站用户ID，多个以英文逗号(,)分割
        });
    });

    //菜单管理
    Route::prefix('menus')->group(function () {
        Route::get('', [MenuController::class, 'listMenus']);
        Route::get('options', [MenuController::class, 'listMenuOptions']);
        Route::get('routes', [MenuController::class, 'listRoutes']);
        Route::get('{id}/form', [MenuController::class, 'getMenuForm']);
        Route::post('', [MenuController::class, 'saveMenu']);
        Route::put('{id}', [MenuController::class, 'updateMenu']);
        Route::delete('{id}', [MenuController::class, 'deleteMenu']); //菜单ID
        Route::patch('{menuId}', [MenuController::class, 'updateMenuVisible']); //菜单ID
    });

    //角色管理
    Route::prefix('roles')->group(function () {
        Route::get('page', [RoleController::class, 'getRolePage']); //角色分页列表
        Route::get('options', [RoleController::class, 'listRoleOptions']); //角色下拉列表
        Route::post('', [RoleController::class, 'addRole']);
        Route::put('{id}', [RoleController::class, 'updateRole']);
        Route::get('{roleId}/form', [RoleController::class, 'getRoleForm']); //角色表单数据
        Route::delete('{ids}', [RoleController::class, 'deleteRoles']); //删除角色ID，多个以英文逗号(,)分割
        Route::put('{roleId}/status', [RoleController::class, 'updateRoleStatus']); //修改角色状态
        Route::get('{roleId}/menuIds', [RoleController::class, 'getRoleMenuIds']); //获取角色的菜单ID集合
        Route::put('{roleId}/menus', [RoleController::class, 'assignMenusToRole']); //分配菜单(包括按钮权限)给角色
    });

    //部门管理
    Route::prefix('dept')->group(function () {
        Route::get('', [DeptController::class, 'listDepartments']);
        Route::get('options', [DeptController::class, 'listDeptOptions']);
        Route::get('{deptId}/form', [DeptController::class, 'getDeptForm']);
        Route::post('', [DeptController::class, 'saveDept']);
        Route::put('{deptId}', [DeptController::class, 'updateDept']);
        Route::delete('{ids}', [DeptController::class, 'deleteDepartments']); //部门ID，多个以英文逗号(,)分割
    });

    //字典管理
    Route::prefix('dict')->group(function () {
        Route::get('page', [DictController::class, 'getDictPage']); //字典分页列表
        Route::get('list', [DictController::class, 'getAllDictWithData']); //所有字典列表
        Route::get('{id}/form', [DictController::class, 'getDictForm']); //字典数据表单数据
        Route::post('', [DictController::class, 'saveDict']);
        Route::put('{id}', [DictController::class, 'updateDict']);
        Route::delete('{ids}', [DictController::class, 'deleteDict']); //字典ID，多个以英文逗号(,)分割
    });

    Route::prefix('dict-data')->group(function () {
        Route::get('page', [DictDataController::class, 'getDictDataPage']); //字典分页列表
        Route::get('{id}/form', [DictDataController::class, 'getDictDataForm']); //字典数据表单数据
        Route::get('{dictCode}/options', [DictDataController::class, 'getDictDataList']); //字典数据列表
        Route::post('', [DictDataController::class, 'saveDictData']);
        Route::put('{id}', [DictDataController::class, 'updateDictData']);
        Route::delete('{ids}', [DictDataController::class, 'deleteDictData']); //字典ID，多个以英文逗号(,)分割
    });

    //配置管理
    Route::prefix('config')->group(function () {
        Route::get('page', [ConfigController::class, 'getConfigPage']); //配置分页列表
        Route::get('{id}/form', [ConfigController::class, 'getConfigForm']); //配置数据表单数据
        Route::put('refresh', [ConfigController::class, 'refreshCache']);
        Route::post('', [ConfigController::class, 'saveConfig']);
        Route::put('{configId}', [ConfigController::class, 'updateConfig']);
        Route::delete('{ids}', [ConfigController::class, 'deleteConfig']); //配置id，多个以英文逗号(,)分割
        Route::get('{key}/value', [ConfigController::class, 'getSystemConfig']);
    });

    Route::prefix('notices')->group(function () {
        Route::get('page', [NoticeController::class, 'getNoticePage']); //
        Route::get('my-page', [NoticeController::class, 'getMyNoticePage']); //获取我的通知公告分页列表
        Route::get('{id}/form', [NoticeController::class, 'getNoticeForm']); //获取通知公告表单数据
        Route::get('{id}/detail', [NoticeController::class, 'getNoticeDetail']); //阅读获取通知公告详情
        Route::post('', [NoticeController::class, 'saveNotice']);
        Route::put('read-all', [NoticeController::class, 'readAll']);
        Route::put('{id}', [NoticeController::class, 'updateNotice']);
        Route::put('{id}/publish', [NoticeController::class, 'publishNotice']); //发布通知公告
        Route::put('{id}/revoke', [NoticeController::class, 'revokeNotice']); //撤回通知公告
        Route::delete('{ids}', [NoticeController::class, 'deleteNotices']); //配置id，多个以英文逗号(,)分割
    });

    //日志
    Route::prefix('logs')->group(function () {
        Route::get('page', [LogController::class, 'listPagedLogs']); //日志分页列表
        Route::get('visit-trend', [LogController::class, 'getVisitTrend']); //获取访问趋势
        Route::get('visit-stats', [LogController::class, 'getVisitStats']); //获取访问统计
    });

});



//文件管理
Route::prefix('files')->group(function () {
    Route::post('', [FileController::class, 'uploadFile']); //文件上传
    Route::delete('', [FileController::class, 'deleteFile']); //文件删除

});


