<?php
/**
 *
 * @description
 * @author wentao
 * @DateTime 2023/5/25
 */

namespace Wentao\PushLogToPlume\Transport;

use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

class ByHttp implements Transport
{
    static array $transport = [];
    private string $url;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->url = sprintf('%s/sendLog?logKey=%s', $config['http']['host'], $config['queue_name']);

        app('events')->listen(RequestHandled::class, fn() => $this->destruct());
    }

    public function send(Message $message): void
    {
        self::$transport[] = json_decode($message->toString(), true);
    }

    public function destruct()
    {
        Http::post($this->url, self::$transport);
    }
}
