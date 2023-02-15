<?php

namespace StreakSymfony\Base\Service;

use Symfony\Component\HttpFoundation\Request;

interface IActionService
{
    public function getClassName() : string;
    public function setRequest(Request $request);
}