<?php

namespace StreakSymfony\Base\Service;

use StreakSymfony\Base\Traits\ClassNameTrait;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractActionService implements IActionService
{
    use ClassNameTrait;

    protected $request;

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }
}