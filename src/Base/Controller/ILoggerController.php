<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Controller;

use Psr\Log\LoggerInterface;

interface ILoggerController
{
    public function getLogger():LoggerInterface;
}
