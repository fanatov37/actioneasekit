<?php

namespace ActionEaseKit\Base\Service;

use Enqueue\Client\ProducerInterface;
use Psr\Log\LoggerInterface;
use ActionEaseKit\Base\Traits\ClassNameTrait;

class RabbitProducerService
{
    use ClassNameTrait;

    private const SLEEP_COUNT = 5;

    /**
     * @deprecated
     *
     * new rabbit resolve this. try and remove if need
     */
    private int $maxTryCount = 10;

    /** @codeCoverageIgnore  */
    public function __construct(private ProducerInterface $producer, private LoggerInterface $logger)
    {}

    public function sendCommand (string $command, $message, bool $needReply = false) : void
    {
        while ($this->maxTryCount) {
            try {
                $this->producer->sendCommand($command, $message, $needReply);

                break;
            } catch (\Throwable $exception) {
                --$this->maxTryCount;

                sleep(self::SLEEP_COUNT);
            }
        }

        if (!$this->maxTryCount) {
            $this->logger->critical($this->getClassName(), [
                'command' => $command,
                'message' => $message,
                'maxTryCount' => $this->maxTryCount,
                'errorMsg' => $exception->getMessage()
            ]);
        }

        if ($this->maxTryCount < 10) {
            $this->logger->warning($this->getClassName(), [
                'command' => $command,
                'message' => $message,
                'maxTryCount' => $this->maxTryCount,
                'msg' => 'Was a problem'
            ]);
        }
    }
}
