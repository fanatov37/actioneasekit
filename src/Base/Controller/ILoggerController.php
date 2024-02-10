<?php

namespace ActionEaseKit\Base\Controller;

use Psr\Log\LoggerInterface;

interface ILoggerController
{
    public function getLogger():LoggerInterface;
}
