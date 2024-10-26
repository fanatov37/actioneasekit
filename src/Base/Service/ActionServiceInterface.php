<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Service;

use Symfony\Component\HttpFoundation\Request;

interface ActionServiceInterface
{
    public function getClassName() : string;
    public function setRequest(Request $request);
    public function checkAccess(string $action) : void;
}
