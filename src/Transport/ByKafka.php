<?php
/**
 *
 * @description
 * @author wentao
 * @DateTime 2023/5/25
 */

namespace Wentao\PushLogToPlume\Transport;

use longlang\phpkafka\Producer\Producer;
use longlang\phpkafka\Producer\ProducerConfig;

class ByKafka implements Transport
{

    private Producer $producer;

    public function __construct(array $config)
    {
        $config = $config['kafka'];
        $kafka_config = new ProducerConfig();
        $kafka_config->setConnectTimeout($config['timeout']);
        $kafka_config->setBootstrapServer($config['host']);
        $kafka_config->setUpdateBrokers(true);
        $kafka_config->setAcks(-1);
        $this->producer = new Producer($kafka_config);

    }

    public function send(Message $message): void
    {
        $this->producer->send(config('plume.queue_name'), $message->toString());
    }

    public function __destruct()
    {
        $this->producer->close();
    }
}
