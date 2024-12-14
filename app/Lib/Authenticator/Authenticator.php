<?php
namespace App\Lib\Authenticator;

use App\Exceptions\Token\DeployException;
use App\Services\System\LoginService;
use Exception;
use Illuminate\Http\Request;
use ReflectionClass;
use ReflectionException;
use WangYu\Reflex;

class Authenticator
{

    private $parsedClass;

    public function __construct(Request $request)
    {
        // 获取当前请求的控制层
        $controller = $request->route()->getActionName();
        // 控制层下有二级目录，需要解析下。如controller/cms/Admin，获取到的是Cms.Admin
        $controllerPath = explode('@', $controller);
        // 获取当前请求的方法
        $action =$controllerPath[1];
        // 反射获取当前请求的控制器类
        $class = new ReflectionClass($controllerPath[0]);
        $this->parsedClass = (new Reflex($class->newInstance()))->setMethod($action);
    }

    /**
     * 入口方法
     * @return bool
     * @throws DeployException
     * @throws ReflectionException
     */
    public function check(): bool
    {
        //判断是否开启加载文件函数注释
        if (ini_get('opcache.save_comments') === '0' || ini_get('opcache.save_comments') === '') {
            throw new DeployException(message('no.permission'));
        }
        // 执行校验并返回校验结果
        return $this->execute();

    }

    /**
     * 执行各权限等级校验
     * @param string $actionPermissionLevel
     * @return bool
     * @throws ReflectionException
     */
    public function execute(): bool
    {
        $actionPermission = $this->actionPermission();
        if (empty($actionPermission)) return true;

        // 角色列表
        $roles = $this->getRoles();

        //账户属于超级管理员，直接通过
        if (LoginService::getInstance()->isRoot()) {
            return true;
        }

        //获取注解权限
        $method = $actionPermission[0] ?? '';
        $actionPermissionName = $actionPermission[1] ?? '';

        return AuthenticatorExecutorFactory::getInstance($method)->handle($roles, $actionPermissionName);

    }

    protected function getUserInfo()
    {
        return LoginService::getInstance()->user();
    }

    protected function getRoles()
    {
        return LoginService::getInstance()->getRoles();
    }

    /**
     * 获取接口权限注解内容
     * @return string
     * @throws Exception
     */
    protected function actionPermission(): array
    {
        $actionAuthContent = $this->parsedClass->get('permission');
        $actionAuthContent = $actionAuthContent ?: [];
        return $actionAuthContent;
    }
}
