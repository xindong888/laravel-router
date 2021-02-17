#laravel 万能路由库 

##使用方法一

###1.使用composer加载 composer require xindong888/laravel-router 

###2.进入配置文件夹config->app.php

```php
<?php
[
'providers' => [
//.................注释掉原有的路由服务提供者
//App\Providers\RouteServiceProvider::class,
//.................添加万能路由服务提供者
xindong888\Laravel\Providers\RouteServiceProvider::class
]]
?>
```

##使用方法二

###1. 用app->Providers->RouteServiceProvider继承xindong888\Laravel\Providers\RouteServiceProvider

###2. 清理掉boot()内的代码添加parent::boot();
````php

class RouteServiceProvider extends \xindong888\Laravel\Providers\RouteServiceProvider
{

    public function boot()
    {
        parent::boot();
    }

}

````
