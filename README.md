# push-log-to-plume
## laravel 框架日志推送到 Plumelog
### 1. 开源项目说明
文档地址：https://gitee.com/plumeorg/plumelog/blob/master/FASTSTART.md
### 2. 安装
```shell
 composer require wentao-php/push-log-to-plume
```
### 3. 发布配置
```shell
php artisan push-log-to-plume:install
```
### 4. 说明
- 当`plumelog`设置为`plumelog.model=redis`,此时有两种方案推送日志
1. 利用 **api** 推送 `127.0.0.1:8891/sendLog?logKey=plume_log_list`。 插件会在请求结束时将所有日志数据统一当做接口参数传递
2. 将数据直接写入 **redis** 队列中
- 当`plumelog`设置为`plumelog.model=kafka`,此时有一种方案推送日志,plumelog服务不支持api调用
1. 将数据直接写入 **kafka** 队列中
- 配置文件 `plume.php` 中有配置说明
