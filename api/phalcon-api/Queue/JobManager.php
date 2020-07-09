<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Queue;

use Discoveryfy\Constants\CacheKeys;
use Discoveryfy\Exceptions\InternalServerErrorException;
use Enqueue\Consumption\ChainExtension;
use Enqueue\Consumption\Extension\SignalExtension;
use Enqueue\Consumption\QueueConsumer;
use Enqueue\Monitoring\ConsumedMessageStats;
use Enqueue\Monitoring\ConsumerMonitoringExtension;
use Enqueue\Monitoring\GenericStatsStorageFactory;
use Enqueue\Monitoring\SentMessageStats;
use Enqueue\Monitoring\StatsStorage;
//use Enqueue\Redis\RedisContext as RedisQueue;
use Interop\Queue\Consumer;
use Interop\Queue\Context as Queue;
use Interop\Queue\Message;
use Interop\Queue\Processor;
//use Phalcon\Api\Providers\ConfigProvider;
use Monolog\Logger;
use Phalcon\Api\Providers\LoggerProvider;
use Phalcon\Api\Providers\QueueProvider;
//use Phalcon\Config;
use Phalcon\Di\Injectable;
//use Phalcon\Events\Event;
//use Phalcon\Events\Manager;
use Phalcon\Security\Random;
use function Phalcon\Api\Core\envValue;

class JobManager extends Injectable
{
    const NO_JOBS = 'enqueue.empty';

    /************\
     * Producer *
    \************/

    public function addJob(string $worker = '', array $arguments = [], array $headers = []): void
    {
//        $queueConsumer = $this->getJobsQueueConsumer();
//        $queue = $queueConsumer->getContext();
        $queue = $this->getQueueService();
        $msg = $queue->createMessage($worker, $arguments, $headers);
        $msg->setTimestamp(time());
        $queue
            ->createProducer()
            // @ToDo: Delay 5 sec for debounce similar jobs? (like underscorejs throttle function)
//            ->setDeliveryDelay(5000)
            ->send(
                ($queue->createQueue(CacheKeys::getJobQueue())),
                $msg
            );

        $statsStorage = $this->getStatsStorage();
        $statsStorage->pushSentMessageStats(new SentMessageStats(
            (int) (microtime(true) * 1000),
            CacheKeys::getJobQueue(),
            false,
            $msg->getMessageId(),
            $msg->getCorrelationId(),
            $msg->getHeaders(),
            $msg->getProperties()
        ));
    }

    /**
     * Create 'event' => [ 'tasks' ] functionality ?
     *
     * listenEvents -> produceJobs --redis--> consumeJob -> processJob
     * alternative namings:
     * listenEvents -> queueJobs   --redis--> receiveJob -> executeJob
     */
//    public function attach(Manager $eventsManager)
//    {
//        $tasks = $this->getConfig()->path('queue.manager');
//        foreach ($tasks as $event => $task) {
//            $eventsManager->attach($event, function (Event $event, $component, $args) use ($task) {
//                $this->addJob($task, $args);
//            });
//        }
//    }

    /************\
     * Consumer *
    \************/

    public function loopJobs(): void
    {
        $queueConsumer = $this->getJobsQueueConsumer();
        $self = $this;
        $queueConsumer->bindCallback(CacheKeys::getJobQueue(), function (Message $msg, Queue $queue) use ($self) {
            $self->getLogger()->info('JobManager -> loopJobs -> tick');
            return $self->processJob($msg, $queue);
        });
        $queueConsumer->consume();
    }

    public function doJob(): string
    {
        $consumer = $this->getJobsConsumer();
        $msg = $consumer->receiveNoWait();
//        $msg = $consumer->receive();
        if (!$msg) {
            $this->getLogger()->info('JobManager -> doJobs -> No jobs');
            return self::NO_JOBS;
        }
        $statsStorage = $this->getStatsStorage();
        $receivedAt = (int) (microtime(true) * 1000);
        $consumerId = $this->getRandomService()->uuid();

        $rtn = $this->processJob($msg, $this->getQueueService());
        switch ($rtn) {
            case Processor::ACK:
                $consumer->acknowledge($msg);
                break;
            case Processor::REJECT:
                $consumer->reject($msg, false);
                break;
            case Processor::REQUEUE:
                $consumer->reject($msg, true);
                break;
            default:
                throw new InternalServerErrorException('Invalid return');
                break;
        }

        $statsStorage->pushConsumedMessageStats(new ConsumedMessageStats(
            $consumerId,
            (int) (microtime(true) * 1000), // now
            $receivedAt,
            CacheKeys::getJobQueue(),
            $msg->getMessageId(),
            $msg->getCorrelationId(),
            $msg->getHeaders(),
            $msg->getProperties(),
            $msg->isRedelivered(),
            $rtn,
            null, null, null, null, null, null
        ));
        return $rtn;
    }

    public function cleanJobs(): void
    {
        $queueConsumer = $this->getJobsQueueConsumer();
        $queueConsumer->bindCallback(CacheKeys::getJobQueue(), function (Message $msg, Queue $queue) {
//            echo '.';
            return Processor::ACK;
        });
        $queueConsumer->consume();
    }

    public function purgeJobs(): void
    {
        $this->getQueueService()->purgeQueue(
            $this->getQueueService()->createQueue(CacheKeys::getJobQueue())
        );
    }

    protected function processJob(Message $msg, Queue $queue): string
    {
        $worker = $this->createWorker($msg);
        if (!($worker instanceof Processor)) {
            throw new InternalServerErrorException('Worker should be instance of Processor');
        }
//        if (!$queue) {
//            $queue = $this->getQueueService();
//        }
        $this->getLogger()->info('Executing Worker '.get_class($worker), $msg->getProperties());
        return $worker->process($msg, $queue);
    }

    protected function createWorker(Message $msg)
    {
        $worker = $msg->getBody();
        $attrs = $msg->getProperties();
        if (!class_exists($worker)) {
            throw new InternalServerErrorException(sprintf('Worker %s does not exist', $worker));
        }
        return new $worker($attrs);
    }

    protected function getJobsConsumer(): Consumer
    {
        $queue = $this->getQueueService();
        return $queue->createConsumer(
            ($queue->createQueue(CacheKeys::getJobQueue()))
        );
    }

    protected function getJobsQueueConsumer(): QueueConsumer
    {
        $queue = $this->getQueueService();
        $statsStorage = $this->getStatsStorage();
        $queueConsumer = new QueueConsumer($queue, new ChainExtension([
            new SignalExtension(), //The pcntl extension is required in order to catch signals.
            new ConsumerMonitoringExtension($statsStorage)
        ]));
        return $queueConsumer;
    }

    protected function getQueueService(): Queue
    {
        return $this->getDI()->getShared(QueueProvider::NAME);
//        return $this->queue;
    }

    protected function getLogger(): Logger
    {
        return $this->getDI()->getShared(LoggerProvider::NAME);
//        return $this->logger;
    }

    protected function getStatsStorage(): StatsStorage
    {
        return (new GenericStatsStorageFactory())->create([
            'dsn' => sprintf(
                'influxdb://%s:%s',
                envValue('INFLUXDB_HOST', '127.0.0.1'),
                envValue('INFLUXDB_PORT', '8086')
            ),
            'db' => envValue('INFLUXDB_DATABASE', 'discoveryfy'),
            'user' => envValue('INFLUXDB_USER', 'root'),
            'password' => envValue('INFLUXDB_PASSWORD', '5up3rS3cr3t'),
        ]);
    }

    protected function getRandomService(): Random
    {
        return (new Random());
    }
//    protected function getConfig(): Config
//    {
//        return $this->getDI()->getShared(ConfigProvider::NAME);
////        return $this->config;
//    }
}
