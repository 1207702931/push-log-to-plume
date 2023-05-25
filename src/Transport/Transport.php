<?php
/**
 *
 * @description
 * @author wentao
 * @DateTime 2023/5/25
 */

namespace Wentao\PushLogToPlume\Transport;

interface Transport
{
    public function __construct(array $config);
    public function send(Message $message): void;
}
