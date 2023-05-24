<?php
/**
 *
 * @description
 * @author ${USER}
 * @DateTime 2023/5/24
 */

namespace Wentao\PushLogToPlume\Providers;

use Illuminate\Log\Events\MessageLogged;
use Illuminate\Redis\RedisManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Wentao\PushLogToPlume\Listeners\PushLogToPlumeListener;

class PushLogToPlumeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (config('plume.is_enabled')) {
            $this->app['events']->listen(MessageLogged::class, [PushLogToPlumeListener::class, 'handle']);
            $this->app->singleton('logging.plume.traceId', fn() => Str::uuid()->toString());
            $this->app->singleton('logging.plume.redis', function ($app) {
                $config = config('plume.redis');
                $database_redis_config = config('database.redis');
                return new RedisManager($app, Arr::pull($database_redis_config, 'client', 'phpredis'), $config);
            });
        }
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/plume.php' => config_path('plume.php'),
        ]);
    }
}
