<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Service;

use ActionEaseKit\Base\Traits\ClassNameTrait;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractActionService implements IActionService
{
    use ClassNameTrait;

    protected $request;

    public function setRequest(Request $request) : void
    {
        $this->request = $request;
    }
}
