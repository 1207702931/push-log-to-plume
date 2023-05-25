<?php
/**
 *
 * @description
 * @author ${USER}
 * @DateTime 2023/5/24
 */

namespace Wentao\PushLogToPlume\Providers;

use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\ServiceProvider;
use Wentao\PushLogToPlume\Console\Commands\PushLogToPlumeCommand;
use Wentao\PushLogToPlume\Listeners\PushLogToPlumeListener;
use Wentao\PushLogToPlume\Transport\ByHttp;
use Wentao\PushLogToPlume\Transport\ByKafka;
use Wentao\PushLogToPlume\Transport\ByRedis;

class PushLogToPlumeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PushLogToPlumeCommand::class
            ]);
        }
        if (config('plume.is_enabled')) {
            $this->app['events']->listen(MessageLogged::class, [PushLogToPlumeListener::class, 'handle']);
            $this->app->singleton('logging.plume.transport', function () {
                $config = config('plume');
                return match ($config['driver']) {
                    'redis' => new ByRedis($config),
                    'kafka' => new ByKafka($config),
                    'http' => new ByHttp($config),
                    default => throw new \Exception('仅支持 http, redis, kafka 三种方式推送日志!'),
                };
            });
        }
    }

    public
    function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/plume.php' => config_path('plume.php'),
        ], 'push-log-to-plume');
    }
}
