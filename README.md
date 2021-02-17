# laravel 万能路由库

> 优点1. 自动匹配控制器
>
> > 例如:
> >
> > 网址:localhost/home/hello/index/id/name/......
> >
> > 控制器: App\Http\Controllers\Hello.php
> >
> > 方法: index是Hello.php下的方法
> >
> > 参数: id,name.....都是参数,自动匹配到控制器方法内的参数
>
> 优点2. 可以在原有的routes->api.php和routes->web.php内指定路由
>
> 优点3. 在api或者web指定的路由会优先匹配

## 使用方法一

### 1.使用composer加载 composer require xindong888/laravel-router

### 2.进入配置文件夹config->app.php

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

## 使用方法二

### 1. 用app->Providers->RouteServiceProvider继承xindong888\Laravel\Providers\RouteServiceProvider

### 2. 清理掉boot()内的代码添加parent::boot();

````php

class RouteServiceProvider extends \xindong888\Laravel\Providers\RouteServiceProvider
{

    public function boot()
    {
        parent::boot();
    }

}

````
