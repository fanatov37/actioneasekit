<?php

namespace App\Base\Traits;

trait ClassNameTrait
{
    public function getClassName(): string
    {
        $result = explode('\\', $this::class);

        return end($result);
    }

    public function getClassNameByClass(string $className): string
    {
        $result = explode('\\', $className);

        return end($result);
    }
}
