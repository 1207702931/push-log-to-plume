<?php
/**
 *
 * @description
 * @author ${USER}
 * @DateTime 2023/5/24
 */

namespace Wentao\PushLogToPlume\Listeners;

use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Wentao\PushLogToPlume\Transport\Message;

class PushLogToPlumeListener
{
    private static string $uuid;
    const PUSH_ERROR_MESSAGE = 'plume push error: ';

    public function __construct()
    {
    }

    public function handle(MessageLogged $event): void
    {
        // 推送失败打印日志后，防止循环调用
        if ($event->level == 'error' && str_starts_with($event->message, self::PUSH_ERROR_MESSAGE)) {
            return;
        }
        # 单个请求用同一个的 traceId
        if (self::$uuid == null) {
            // TODO: 链路追踪
            self::$uuid = request()->header('sw8') ?: Str::uuid()->toString();
        }
        $content = $event->context;
        if ($content['exception'] ?? false) {
            $content['exception'] = $content['exception']->getTraceAsString();
        }

        $message = new Message(self::$uuid, strtoupper($event->level), json_encode([
            'message' => $event->message,
            'content' => $content
        ], JSON_UNESCAPED_UNICODE));

        try {
            app('logging.plume.transport')->send($message);
        } catch (\Exception $e) {
            Log::error(self::PUSH_ERROR_MESSAGE . $e->getMessage());
        }
    }
}
