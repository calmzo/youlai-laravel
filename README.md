# 开发环境

`PHP v8.0`  `MySQL 5.7.38` [Laravel v10](https://learnku.com/docs/laravel/10.x)

# Api文档地址


# 部署

## Nginx 伪静态

``` 
location / {
try_files $uri $uri/ /index.php?$query_string;
}
``` 

# 文件目录说明

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

# composer

## composer

`composer install`

# Laravel Artisan

## 秘钥

```
php artisan key:generate
```

## jwt
```shell
php artisan jwt:secret
```

# 问题
```markdown
1、This password does not use the Bcrypt algorithm.
使用非 Bcrypt 哈希值的密码，Hash:make 初始化user密码
2、修改前端路由
api/v1/auth/login 修改为 admin/auth/login

```


## 队列

### 生成任务类

### 运行队列

### 任务ID 在命令行输出

### 队列进程

### 删除失败的任务

#### 删除失败记录

### 数据库

#### 更新数据库

`php artisan migrate`

#### 数据库回滚迁移

`php artisan migrate:refresh --seed`

#### 数据库迁移文件

#### 生成数据填文件

`php artisan make:seeder UserSeeder`

#### 数据填充

`php artisan db:seed`

### 生成路由缓存

`php artisan route:cache`

### 任务调度

#### 查看任务计划的概述及其下次计划运行时间

`php artisan schedule:list`

#### 本地测试

`php artisan schedule:work`
