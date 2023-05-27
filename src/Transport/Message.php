<?php
/**
 *
 * @description
 * @author wentao
 * @DateTime 2023/5/25
 */

namespace Wentao\PushLogToPlume\Transport;

class Message
{
    /**
     *
    "appName":"应用名称",
    "serverName":"服务器IP地址",
    "dtTime":"时间戳的时间格式",
    "traceId":"自己生成的traceid",
    "content":"日志内容",
    "logLevel":"日志等级 INFO ERROR WARN ERROR大写",
    "className":"产生日志的类名",
    "method":"产生日志的方法",
    "logType":"1",
    "dateTime":"2020-12-25 10:10:10"
     */
    private string $appName;
    private string $serverName;
    private int $dtTime;
    private string $traceId;
    private string $content;
    private string $logLevel;
    private string $className = '产生日志的类名';
    private string $method = '产生日志的方法';
    private string $logType = '产生日志的方法';
    private string $dateTime;

    public function __construct($traceId, $logLevel, $content)
    {
        [$float, $timestamp] = explode(' ', microtime());
        $this->appName = config('plume.app_name');
        $this->serverName = request()->ip();
        $this->dtTime = (int)($timestamp . substr($float, 2, 3));
        $this->dateTime = date('Y-m-d H:i:s');
        // 利用参数判断，获取产生日志的类，方法。可能不太准确。由于 debug_backtrace 会消耗一定的性能，所以加参数才记录
        if (request()->input('logging') == 'trace') {
            $deep = config('plume.trace_deep');
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $deep + 1);
            if (isset($trace[$deep])) {
                $this->className = $trace[$deep]['class'] ?? '';
                $this->method = $trace[$deep]['function'] ?? '';
            }
        }
        $this->traceId = $traceId;
        $this->logLevel = $logLevel;
        $this->content = $content;

    }

    /**
     * @param string $appName
     */
    public function setAppName(string $appName): void
    {
        $this->appName = $appName;
    }

    /**
     * @param string $serverName
     */
    public function setServerName(string $serverName): void
    {
        $this->serverName = $serverName;
    }

    /**
     * @param string $dtTime
     */
    public function setDtTime(string $dtTime): void
    {
        $this->dtTime = $dtTime;
    }

    /**
     * @param string $traceId
     */
    public function setTraceId(string $traceId): void
    {
        $this->traceId = $traceId;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @param string $logLevel
     */
    public function setLogLevel(string $logLevel): void
    {
        $this->logLevel = $logLevel;
    }

    /**
     * @param string $className
     */
    public function setClassName(string $className): void
    {
        $this->className = $className;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * @param string $logType
     */
    public function setLogType(string $logType): void
    {
        $this->logType = $logType;
    }

    /**
     * @param string $dateTime
     */
    public function setDateTime(string $dateTime): void
    {
        $this->dateTime = $dateTime;
    }

    public function toString()
    {
        return json_encode([
            'appName' => $this->appName,
            'serverName' => $this->serverName,
            'dtTime' => $this->dtTime,
            'traceId' => $this->traceId,
            'content' => $this->content,
            'logLevel' => $this->logLevel,
            'className' => $this->className,
            'method' => $this->method,
            'logType' => $this->logType,
            'dateTime' => $this->dateTime,
        ], JSON_UNESCAPED_UNICODE);
    }
}
