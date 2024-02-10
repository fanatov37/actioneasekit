<?php

namespace ActionEaseKit\Base\Service;

interface ValidationInterface
{
    const POSTFIXUS = 'Validation';

    public function getValidationClass() : string|object;
}
