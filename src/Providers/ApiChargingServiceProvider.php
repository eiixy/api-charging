<?php

namespace Eiixy\ApiCharging\Providers;


use Eiixy\ApiCharging\Http\Middleware\ApiCharging;
use Illuminate\Support\ServiceProvider;

class ApiChargingServiceProvider extends ServiceProvider
{
    protected $middlewareAliases = [
        'charging' => ApiCharging::class
    ];

    public function boot()
    {
        // 发布配置文件
        $config_path = realpath(__DIR__.'/../../config/rbac.php');
        $this->publishes([
            $config_path => database_path('config/rbac.php'),
        ], 'rbac-config');

        $this->publishes([
            __DIR__.'/../../database/seeds' => database_path('seeds'),
        ], 'rbac-seeds');

        // 加载数据库迁移文件
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // 加载路由
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');

        // 加载中间件
        $this->aliasMiddleware();

    }


    protected function loadMigrationsFrom($paths)
    {
        $this->app->afterResolving('migrator', function ($migrator) use ($paths) {
            foreach ((array) $paths as $path) {
                $migrator->path($path);
            }
        });
    }

    protected function aliasMiddleware()
    {
        $router = $this->app['router'];
        $method = method_exists($router, 'aliasMiddleware') ? 'aliasMiddleware' : 'middleware';

        foreach ($this->middlewareAliases as $alias => $middleware) {
            $router->$method($alias, $middleware);
        }
    }
}
