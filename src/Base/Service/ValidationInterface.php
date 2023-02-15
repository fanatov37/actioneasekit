<?php

namespace App\Base\Service;

interface ValidationInterface
{
    const POSTFIXUS = 'Validation';

    public function getValidationClass() : string|object;
}
