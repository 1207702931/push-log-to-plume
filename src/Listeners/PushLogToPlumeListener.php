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

class PushLogToPlumeListener
{
    private static $uuid;
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
            self::$uuid = request()->header('sw8') ?: app('logging.plume.traceId');
        }
        $content = $event->context;
        if ($content['exception'] ?? false) {
            $content['exception'] = $content['exception']->getTraceAsString();
        }
        [$float, $timestamp] = explode(' ', microtime());
        $item = [
            "appName" => config('plume.app_name'),
            "serverName" => request()->ip(),
            "dtTime" => $timestamp . substr($float, 2, 3), // "毫秒时间戳的时间格式",
            "traceId" => self::$uuid, // "自己生成的traceId",
            "content" => json_encode($content, JSON_UNESCAPED_UNICODE), // "日志内容",
            "logLevel" => strtoupper($event->level), // "日志等级 INFO ERROR WARN ERROR大写",
            "className" => "产生日志的类名",
            "method" => "产生日志的方法",
            "logType" => "1",
            "dateTime" => date('Y-m-d H:i:s'), // "时间"
        ];

        # 利用参数判断，获取产生日志的类，方法。可能不太准确。由于 debug_backtrace 会消耗一定的性能，所以加参数才记录
        if (request()->input('logging') == 'trace') {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 8);
            if (isset($trace[7])) {
                $item['className'] = $trace[7]['class'] ?? '';
                $item['method'] = $trace[7]['function'] ?? '';
            }
        }
        try {
            // 推入 plume 服务的 redis 队列
            app('logging.plume.redis')->client()->lPush('plume_log_list', json_encode($item, JSON_UNESCAPED_UNICODE));
        } catch (\RedisException $e) {
            Log::error(self::PUSH_ERROR_MESSAGE . $e->getMessage());
        }
    }
}
