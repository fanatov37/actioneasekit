<?php

namespace ActionEaseKit\Base\Consumer;

use Enqueue\AmqpTools\RabbitMqDlxDelayStrategy;
use Enqueue\Client\CommandSubscriberInterface;
use Interop\Queue\Message;
use Interop\Queue\Context;
use Interop\Queue\Processor;
use Enqueue\Util\JSON;
use Psr\Log\LoggerInterface;
use ActionEaseKit\Base\Exception\DelayedException;
use ActionEaseKit\Base\Service\RunCustomCommandService;
use ActionEaseKit\Base\Traits\ClassNameTrait;

abstract class AbstractCmdConsumer implements Processor, CommandSubscriberInterface
{
    use ClassNameTrait;

    const AVAILABLE_PRODUCERS = ['default'];

    public function __construct(
        private RunCustomCommandService $runCustomCommandService,
        private LoggerInterface $logger,
    )
    {}

    /**
     *  return self::REJECT; // when the message is broken
     *  return self::REQUEUE; // the message is fine but you want to postpone processing
     */
    public function process(Message $message, Context $context)
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

            $queue = $context->createQueue($queueName);

            if ($exception->messageData) {
                $body = JSON::decode($message->getBody());
                $body = array_merge($body, $exception->messageData);
                $message->setBody(JSON::encode($body));
            }

            $producer = $context->createProducer();
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

        foreach (static::AVAILABLE_PRODUCERS as $producer) {
            $commands [] = [
                'command' => $producer,
                'queue' => $producer,

                'processor' => static::class,
                'processorName' => static::class,

                'prefix_queue' => false,
                'exclusive' => true,
            ];
        }

        return $commands;
    }
}