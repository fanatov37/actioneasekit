<?php

namespace ActionEaseKit\Base\Traits;

trait ClassNameTrait
{
    public function getClassName(): string
    {
        $result = explode('\\', static::class);

        return end($result);
    }

    public function getClassNameByClass(string $className): string
    {
        $result = explode('\\', $className);

        return end($result);
    }
}
