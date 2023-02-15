<?php

namespace App\Base\Controller;

use Psr\Log\LoggerInterface;

interface ILoggerController
{
    public function getLogger():LoggerInterface;
}
