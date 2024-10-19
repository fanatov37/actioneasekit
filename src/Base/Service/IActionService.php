<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Service;

use Symfony\Component\HttpFoundation\Request;

interface IActionService
{
    public function getClassName() : string;
    public function setRequest(Request $request);
}
