<?php
/**
 *
 * @description
 * @author wentao
 * @DateTime 2023/5/25
 */

namespace Wentao\PushLogToPlume\Transport;

use Illuminate\Redis\RedisManager;
use Illuminate\Support\Arr;

class ByRedis implements Transport
{
    /**
     * @var mixed|\Redis
     */
    private mixed $redis;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $database_redis_config = config('database.redis');
        $this->redis = (new RedisManager(app(), Arr::pull($database_redis_config, 'client', 'phpredis'), config('plume')))->connection('redis')->client();
    }

    public function send(Message $message): void
    {
        $this->redis->lPush(config('plume.queue_name'), $message->toString());
    }

    public function __destruct()
    {
        $this->redis->close();
    }
}
