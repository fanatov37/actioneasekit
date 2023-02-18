<?php

namespace App\Exception;

class DelayedException extends \Exception
{
    private const MIN1_IN_SEC = 60000;

    const DELAY_5_MIN = 5 * self::MIN1_IN_SEC;
    const DELAY_1_MIN = self::MIN1_IN_SEC;

    public function __construct(
        public ?string $queueDelayed = null,
        public int $delay = self::DELAY_5_MIN,
        public array $messageData=[]

    )
    {
        parent::__construct("Send to delayed=>{$this->queueDelayed} | {$this->delay}");
    }
}