<?php

namespace xindong888\Laravel\Providers;

use Exception;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Contracts\ControllerDispatcher;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use ReflectionMethod;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            $prefix = ['front' => 'web', 'back' => 'api'];//定义前后端模块
            Route::prefix($prefix['back'])
                ->middleware($prefix['back'])
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware($prefix['front'])
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            $uri=isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'/';
            //获取短网址
            $url = explode('/', $uri);
            //默认的中间件
            $middleware = $prefix['front'];
            //获取模块中间件
            if (count($url) > 0 && $url[1] === $prefix['back']) {
                $middleware = $prefix['back'];
            }
            /*万能路由*/
            Route::any('/{model?}/{controller?}/{action?}/{params?}',
                function ($module = 'home', $controller = 'index', $action = 'index', $params = '') use ($prefix) {
                    //获取控制的类路径
                    $class = 'App\Http\Controllers\\' . $module . '\\' . ucfirst(strtolower($controller));
                    //判断是不是一个类
                    if (class_exists($class)) {
                        $controller = new $class();//实例化控制器类
                        $p = explode('/', $params);//拆分参数
                        //获取控制器调度
                        $cd = app()->make(ControllerDispatcher::class);
                        try {
                            //包装控制器方法里的参数
                            $param = $cd->resolveMethodDependencies($p, new ReflectionMethod($controller, $action));
                            //调用控制器里的方法并传入参数
                            return $controller->{$action}(...array_values($param));
                        } catch (Exception $exception) {
                            //echo $exception->getMessage();
                        }
                    }
                    if ($module === $prefix['back']) {
                        return 'api404';
                    }
                    return 'web404';
                })->where('params', '.*')->middleware($middleware);
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
