<?php

namespace App\Base\Consumer;

use App\Base\Service\RunCustomCommandService;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

/** @todo move to enqueue */
class CmdConsumer implements ConsumerInterface
{
    public function __construct(private RunCustomCommandService $runCustomCommandService)
    {}

    public function execute(AMQPMessage $msg)
    {
        $body = json_decode($msg->getBody(), true);

        $this->runCustomCommandService->runCmd($body);
    }
}