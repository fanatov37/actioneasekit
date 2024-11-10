<?php

namespace ActionEaseKit\Base\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ValidationAttribute
{
    public const POSTFIXUS = 'Validation';

    public function __construct(public readonly string $validationClass)
    {
    }
}
