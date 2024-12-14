<div align="center">
   <img alt="logo" width="100" height="100" src="http://cdnwm.yuluojishu.com/uploads/20241214/a3f9203493822a56878bddf7b4a7e770.jpeg">
   <h2>youlai-laravel</h2>
   <img alt="Calm" src="https://img.shields.io/badge/PHP -8.0-brightgreen.svg"/>
   <img alt="Calm" src="https://img.shields.io/badge/Laravel-10.48.25-green.svg"/>
   <a href="https://gitee.com/Calm-Chan/youlai-laravel.git" target="_blank">
     <img alt="Calm" src="https://gitee.com/Calm-Chan/youlai-laravel/badge/star.svg"/>
   </a>     
   <a href="https://github.com/calmzo" target="_blank">
     <img alt="Calm" src="https://img.shields.io/github/stars/calmzo/youlai-laravel.svg?style=social&label=Stars"/>
   </a>
   <br/>
   <img alt="Calm" src="https://img.shields.io/badge/license-Apache%20License%202.0-blue.svg"/>
   <a href="https://gitee.com/youlaiorg" target="_blank">
     <img alt="Calm" src="https://img.shields.io/badge/Author-Calm-orange.svg"/>
   </a>
</div>

## 📢 项目简介

**在线预览**: [https://apifox.com/apidoc/shared-1e79237e-8496-47e0-a76f-4d8380c26ec6](https://apifox.com/apidoc/shared-1e79237e-8496-47e0-a76f-4d8380c26ec6)

基于 Laravel10、JWT、Redis、Vue 3、Element-Plus 构建的前后端分离单体权限管理系统。

- **🚀 开发框架**: 使用 Laravel10 和 Vue 3，以及 Element-Plus 等主流技术栈，实时更新。

- **🔐 安全认证**: JWT 提供安全、无状态、分布式友好的身份验证。

- **🔑 权限管理**: 基于反射注解，实现权限控制，涵盖接口方法和按钮级别。

- **🛠️ 功能模块**: 包括用户管理、角色管理、菜单管理、部门管理、字典管理等多个功能。

## 项目目录

``` 
+---Casts
+---Console
+---Enums 枚举
+---Exceptions 异常
+---Http
|   +---Controllers  控制器
|   |   \---Admin //admin 控制器
|   +---Middleware 中间件
+---Inputs 验证器
+---Jobs
+---Lib
|   \---Authenticator 权限
|   \---Excel   导出
+---Listeners
+---Models 模型 只抒写模型关联关系 字段格式转换
|   +---System        权限组
+---Providers
+---Tools 工具类
+---Utils
``` 


## 🌺 前端工程
| Gitee | Github |
|-------|------|
| [vue3-element-admin](https://gitee.com/youlaiorg/vue3-element-admin)  | [vue3-element-admin](https://github.com/youlaitech/vue3-element-admin)  |


## 🌈 接口文档

- `apifox`  在线接口文档：[https://www.apifox.cn/apidoc](https://www.apifox.cn/apidoc/shared-195e783f-4d85-4235-a038-eec696de4ea5)



## 🚀 项目启动

1. **数据库初始化**
   
   执行 database下 [youlai_laravel.sql](database/mysql8/youlai_laravel.sql) 脚本完成数据库创建、表结构和基础数据的初始化。

2. **修改配置**

   复制[.env.dev](.env.dev) 文件,新增 .env文件，修改MySQL、Redis连接配置；

3. **启动项目**

    nginx配置
``` 
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
``` 

```shell
composer install # 依赖包
php artisan key:generate #秘钥
php artisan jwt:secret #生成jwt密钥
```

4. **问题**
   
   1、This password does not use the Bcrypt algorithm.
   
   使用非 Bcrypt 哈希值的密码，Hash:make 初始化user密码
   
    2、修改前端路由
   
    api/v1/auth/login 修改为 admin/auth/login
