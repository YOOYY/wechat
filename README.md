网站根目录设置为/public目录  
运行wechat.sql创建wechat数据表,修改.env文件配置  
正常运行需要填写 .env 文件中 微信配置


1.laravel定时任务需要在php.ini中开启禁用的exec函数  
2.定时任务需要给予artisan执行权限  
3.设置时区 TIMEZONE = Asia/Shanghai  
4.esaywechat日志  
    'log' => [  
        'permission' => 0777,  
        'level' => env('WECHAT_LOG_LEVEL', 'error'),  
        'file'  => env('WECHAT_LOG_FILE', storage_path('logs/easywechat_'.date('Ymd').'.log')),  
    ]  
5.laravel日志权限问题解决办法  
    setfacl -R -d -m user:www:rwx  laravel/storage/logs/  
    setfacl -R -d -m group:www:rwx  laravel/storage/logs/  
同时赋予执行定时任务的qiutao权限  
    setfacl -R -d -m user:qiutao:rwx  laravel/storage/framework/cache/  
    setfacl -R -d -m group:qiutao:rwx  laravel/storage/framework/cache/  
6.发送邮件  
    绑定QQ邮箱的话需要在QQ邮箱设置-账户中开启POP3/IMAP/SMTP/Exchange/CardDAV/CalDAV服务,然后获取密码  
    好像需要定期更换密码，否则会出错  
    
7.迁移出现500错误或者空白页,可是尝试修改/usr/local/nginx/conf/fastcgi.conf 最后一句,改为  
fastcgi_param PHP_ADMIN_VALUE "open_basedir=/home/wwwroot/:/tmp/:/proc/";  

8.控制器首字母大写  
php artisan cache:clear  
php artisan config:clear  

定时任务  
* * * * * php /path-to-your-project/artisan schedule:run  

laravel  
https://laravelacademy.org/laravel-docs-5_1  
easywechat  
https://www.easywechat.com/  
微信公众平台  
https://mp.weixin.qq.com/  

捕获错误 catch(\Exception $e) 一定要加 \  

礼包码功能(xxx 为 礼包名称，必须唯一，已占用名称列表[newplayer])  
在wechat目录下php artisan initGiftCode xxx 生成redis  
然后直接在数据库message里面添加一个type为method,content为xxx的条目  
在这个页面http://test.com/menu/lists添加菜单事件的绑定  
新建菜单提交覆盖原有菜单  
代码见app/Console/Commands/InitGiftCode.php  

版本是easywechat3和laravel5  

定时任务与素材每日更新有关，代码见app/Console/Commands/Update.php和app/Console/Kernel.php  