<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Traits;

/** @codeCoverageIgnore  */
trait ReflectionHelperTrait
{
    public function invokeGetProperty(mixed $object, string $propertyName)
    {
        $reflection = new \ReflectionProperty($object::class, $propertyName);
        $reflection->setAccessible(true);
        return $reflection->getValue($object);
    }

    public function invokeSetProperty(&$object, string $propertyName, $value) : void
    {
        $reflection = new \ReflectionProperty($object::class, $propertyName);
        $reflection->setAccessible(true);
        $reflection->setValue($object, $value);
    }

    public function invokeMethod(&$object, string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass($object::class);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
