<?php

namespace StreakSymfony\Base\Consumer;

use Enqueue\AmqpTools\RabbitMqDlxDelayStrategy;
use Enqueue\Client\CommandSubscriberInterface;
use Interop\Queue\Message;
use Interop\Queue\Context;
use Interop\Queue\Processor;
use Enqueue\Util\JSON;
use Psr\Log\LoggerInterface;
use StreakSymfony\Base\Exception\DelayedException;
use StreakSymfony\Base\Service\RunCustomCommandService;
use StreakSymfony\Base\Traits\ClassNameTrait;

abstract class AbstractCmdConsumer implements Processor, CommandSubscriberInterface
{
    use ClassNameTrait;

    public const AVAILABLE_PRODUCERS = [];

    public function __construct(
        private RunCustomCommandService $runCustomCommandService,
        private LoggerInterface $logger,
    )
    {}

    /**
     *  return self::REJECT; // when the message is broken
     *  return self::REQUEUE; // the message is fine but you want to postpone processing
     */
    public function process(Message $message, Context $session)
    {
        try {
            $body = JSON::decode($message->getBody());

            if ($body['delayed'] ?? false) {
                unset($body['delayed']);
                $message->setBody(JSON::encode($body));
                throw new DelayedException($message->getRoutingKey());
            }

            $this->runCustomCommandService->runCmd($body, catchExceptions: false);

        } catch (DelayedException $exception) {
            $queueName = $exception->queueDelayed ?? $message->getRoutingKey();

            $queue = $session->createQueue($queueName);

            if ($exception->messageData) {
                $message->setBody(JSON::encode($exception->messageData));
            }

            $producer = $session->createProducer();
            $producer->setDelayStrategy(new RabbitMqDlxDelayStrategy())
                     ->setDeliveryDelay($exception->delay)
                     ->send($queue, $message);

        } catch (\Throwable $exception) {
            $this->logger->critical($this->getClassName(), [
                'msg' => $exception->getMessage(),
                'body' => $body
            ]);
        }

        return self::ACK;
    }

    public static function getSubscribedCommand()
    {
        $commands = [];

        foreach (self::AVAILABLE_PRODUCERS as $producer) {
            $commands [] = [
                'command' => $producer,
                'queue' => $producer,

                'processor' => self::class,
                'processorName' => self::class,

                'prefix_queue' => false,
                'exclusive' => true,
            ];
        }

        return $commands;
    }
}