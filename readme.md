#开发环境的搭建
##1.clone 项目源代码
```php
    git clone git@120.27.125.247:/home/www/git/laravel-my.git
```


##2.项目源代码根目录下创建.env文件，内容如下
```php
APP_ENV=local
APP_DEBUG=true
APP_KEY=3TTN5JUf8uLcC3ZxIrszuO9isduc3IKO

DB_HOST=120.27.125.247
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=654321

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync

MAIL_DRIVER=smtp
MAIL_HOST=mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null


```

##3.项目源代码根目录下创建storage目录及如下子目录，并给777权限。用于程序自动生成的一些文件
```php
storage
├── app
├── framework
│   ├── cache
│   ├── services.json
│   ├── sessions
│   │   └── 9afdc4b64294b3a8a2599e253bba6607256d299d
│   └── views
│       └── ffcf2d6b3fcbe3992535d9db2965b563
├── logs
    └── laravel-2015-11-29.log

```

