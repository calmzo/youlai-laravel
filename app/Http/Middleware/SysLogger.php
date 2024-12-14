<?php

namespace App\Http\Middleware;

use App\Enums\SysLogEnum;
use App\Models\System\SysLog;
use App\Services\System\LoginService;
use App\Utils\Constant;
use App\Utils\Ip\AddressUtils;
use Closure;
use Illuminate\Http\Request;
use ReflectionClass;
use ReflectionException;
use WangYu\Reflex;
use Jenssegers\Agent\Agent;

class SysLogger
{

    public function handle(Request $request, Closure $next)
    {
        //解析是否日志注解
        $logAnnotation = $this->getLogAnnotation($request);
        if ($logAnnotation) {
//            $ip          = $request->getClientIp();
            $ip          = $request->server('HTTP_X_FORWARDED_FOR') ?? $request->ip();
            $addressInfo = AddressUtils::getRealAddressByIP($ip);
            $province    = $addressInfo['province'] ?? '';
            $city        = $addressInfo['city'] ?? '';
            $address     = $addressInfo['address'] ?? '';
            $path = '/' . $request->path();

            $userId   = null;
            $username = null;
            // 非登录请求获取用户ID，登录请求在登录成功后获取用户ID
            if ($path != Constant::LOGIN_PATH) {
                $user     = LoginService::getInstance()->user();
                $userId   = $user['id'] ?? 0;
            }
            $time = microtime(true);
            // 执行方法
            $response = $next($request);
//            dd($response->getStatusCode(), $response->getData());
            $executionTime = microtime(true) - $time;
            $executionTime = round($executionTime * 1000);

            // 登录请求获取用户ID
            if (!$userId) {
                $user     = LoginService::getInstance()->user();
                $userId   = $user['id'] ?? 0;
            }

            $content = $logAnnotation[0] ?? ''; //title
            $module  = $logAnnotation[1] ?? SysLogEnum::MODULE_OTHER; //module
            //有log注解添加日志
            $agent = new Agent();
            // 获取客户端操作系统
            $os = $agent->platform();
            //获取客户端浏览器
            $browser        = $agent->browser();
            $browserVersion = $agent->version($browser);
            $statusCode     = $response->getStatusCode();
            //创建日志记录
            $log                  = SysLog::new();
            $log->content         = $content;
            $log->module          = $module;
            $log->create_by         = $userId;
            $log->response_content    = $response->getData();
            $log->method          = $request->method();
            $log->request_uri            = $path;
            $log->request_params          = $request->all();
            $log->province        = $province ?? '';
            $log->city            = $city ?? '';
            $log->ip              = $ip;
            $log->browser         = $browser;
            $log->browser_version = $browserVersion;
            $log->os              = $os;
            $log->execution_time  = $executionTime;
            $log->save();
        } else {
            // 执行方法
            $response = $next($request);
        }
        return $response;
    }

    private function getLogAnnotation($request)
    {
        // 获取当前请求的控制层
        $controller = $request->route()->getActionName();
        // 控制层下有二级目录，需要解析下。如controller/cms/Admin，获取到的是Cms.Admin
        $controllerPath = explode('@', $controller);
        // 获取当前请求的方法
        $action = $controllerPath[1];
        // 反射获取当前请求的控制器类
        $class         = new ReflectionClass($controllerPath[0]);
        $parsedClass   = (new Reflex($class->newInstance()))->setMethod($action);
        $logAnnotation = $parsedClass->get('logAnnotation');
        return $logAnnotation;

    }


}
