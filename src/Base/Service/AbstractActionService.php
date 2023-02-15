<?php

namespace App\Base\Service;

use App\Base\Traits\ClassNameTrait;
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