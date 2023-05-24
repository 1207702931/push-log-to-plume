<?php
/**
 *
 * @description
 * @author ${USER}
 * @DateTime 2023/5/24
 */

namespace Wentao\PushLogToPlume\Console\Commands;

use Illuminate\Console\Command;
use longlang\phpkafka\Producer\Producer;
use longlang\phpkafka\Producer\ProducerConfig;
use longlang\phpkafka\Protocol\RecordBatch\RecordHeader;

class T extends Command
{
    protected $signature = 't';

    protected $description = 'Command description';

    public function handle(): void
    {
        $config = new ProducerConfig();
        $config->setBootstrapServer('127.0.0.1:9092');
        $config->setUpdateBrokers(true);
        $config->setAcks(-1);
        $producer = new Producer($config);
        $topic = 'test';
        $value = (string) microtime(true);
        $key = uniqid('', true);
        $producer->send('test', $value, $key);

// 指定 headers
// key-value或使用 RecordHeader 对象，都可以
        $headers = [
            'key1' => 'value1',
            (new RecordHeader())->setHeaderKey('key2')->setValue('value2'),
        ];
        $a = $producer->send('test', $value, $key, $headers);

        dd($a);
    }
}
