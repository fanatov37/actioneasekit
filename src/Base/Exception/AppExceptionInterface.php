<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Exception;

interface AppExceptionInterface
{
    public function getHttpCode() : int;
}
