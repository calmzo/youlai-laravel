<?php

namespace App\Utils;

class CodeResponse
{
    //通用
    const SUCCESS = ['00000', '一切ok'];
    const FAIL = ['00001', '错误'];

    const USER_ERROR = ['A0001', '用户端错误'];
    const REPEAT_SUBMIT_ERROR = ['A0002', '您的请求已提交，请不要重复提交或等待片刻再尝试。'];
    const USER_LOGIN_ERROR = ['A0200', '用户登录异常'];


    const AUTHORIZED_ERROR = ['A0300', '访问权限异常'];
    const ACCESS_UNAUTHORIZED = ['A0301', '访问未授权'];
    const FORBIDDEN_OPERATION = ['A0302', '演示环境禁止新增、修改和删除数据，请本地部署后测试'];

    const USERNAME_OR_PASSWORD_ERROR = ['A0210', '用户名或密码错误'];
    const PASSWORD_ENTER_EXCEED_LIMIT = ['A0211', '用户输入密码次数超限'];
    const CLIENT_AUTHENTICATION_FAILED = ['A0212', '客户端认证失败'];

    const VERIFY_CODE_TIMEOUT = ['A0213', '验证码已过期'];
    const VERIFY_CODE_ERROR = ['A0214', '验证码错误'];

    const TOKEN_INVALID = ['A0230', 'token无效或已过期'];
    const TOKEN_ACCESS_FORBIDDEN = ['A0231', 'token已被禁止访问'];

    const PARAM_ERROR = ['A0400', '用户请求参数错误'];
    const RESOURCE_NOT_FOUND = ['A0401', '请求资源不存在'];
    const PARAM_NOT_EMPTY = ['A0402', '参数不存在'];
    const PARAM_IS_NULL = ['A0410', '请求必填参数为空'];
    const RESOURCE_EXIST = ['A0411', '请求资源已存在'];



    const SYSTEM_EXECUTION_ERROR = ['B0001', '系统执行出错'];
    const SYSTEM_EXECUTION_TIMEOUT = ['B0100', '系统执行超时'];
    const SYSTEM_ORDER_PROCESSING_TIMEOUT = ['B0100', '系统订单处理超时'];



}
