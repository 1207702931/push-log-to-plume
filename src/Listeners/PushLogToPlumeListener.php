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
    private static ?string $uuid = null;
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
            // 首先从 header 中获取，header 中没有再获取 常量中的 SW_TRACE_ID，再没有就自己生成
            // 这里是结合 skywalking 的 链路追踪，
            // java 开启了 skywalking 的链路追踪 Java调用 php 接口 时会在 header 中加上特定值
            // 如何使PHP启动此时 skywalking_agent php 二开后加入了常量 SW_TRACE_ID
            // 如果没有开启则 自己生成 日志的请求ID

            if ($h_sw8 = request()->header('sw8')) {
                // header
                $e_h_sw8 = explode('-', $h_sw8);
                if (count($e_h_sw8) == 8) {
                    self::$uuid = base64_decode($e_h_sw8[1]);
                }
            } elseif (defined('SW_TRACE_ID')) {
                // 常量
                self::$uuid = constant("SW_TRACE_ID");
            } else {
                self::$uuid = Str::uuid()->toString();
            }

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
