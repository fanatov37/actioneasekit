<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Service;

interface ValidationInterface
{
    const POSTFIXUS = 'Validation';

    public function getValidationClass() : string|object;
}
