<?php
/**
 *
 * @description
 * @author wentao
 * @DateTime 2023/5/25
 */

namespace Wentao\PushLogToPlume\Transport;

use Illuminate\Support\Facades\Http;

class ByHttp implements Transport
{
    static array $transport = [];
    private string $url;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->url = sprintf('%s?logKey=%s', $config['http']['host'], $config['queue_name']);
    }

    public function send(Message $message): void
    {
        self::$transport[] = $message->toString();
    }

    public function __destruct()
    {
        Http::post($this->url, self::$transport);
    }
}
