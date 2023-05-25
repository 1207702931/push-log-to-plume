<?php
/**
 *
 * @description
 * @author ${USER}
 * @DateTime 2023/5/25
 */

namespace Wentao\PushLogToPlume\Console\Commands;

use Illuminate\Console\Command;

class PushLogToPlumeCommand extends Command
{
    protected $signature = 'push-log-to-plume:install';

    protected $description = '发布配置文件';

    public function handle(): void
    {
        $this->call('vendor:publish', [
            '--tag' => 'push-log-to-plume',
            '--force' => true
        ]);
    }
}
