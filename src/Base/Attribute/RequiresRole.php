<?php

namespace ActionEaseKit\Base\Attribute;

#[\Attribute(\Attribute::TARGET_METHOD)]
class RequiresRole
{
    public function __construct(private readonly array $roles)
    {
    }

    public function getRoles(): array
    {
        return $this->roles;
    }
}
