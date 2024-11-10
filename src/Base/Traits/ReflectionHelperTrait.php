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

    public function getClassAttributes(?string $name = null) : array|\ReflectionAttribute
    {
        $reflectionClass = new \ReflectionClass(static::class);
        $attributes = $reflectionClass->getAttributes($name);

        return $name ? ($attributes[0] ?? []) : $attributes;
    }

    public function getFunctionAttributes(string $action, ?string $name = null): array|\ReflectionAttribute
    {
        if (!method_exists(static::class, $action)) {
            throw new \InvalidArgumentException("Method $action does not exist in " . static::class);
        }

        $reflectionMethod = new \ReflectionMethod(static::class, $action);
        $attributes = $reflectionMethod->getAttributes($name);

        return $name ? ($attributes[0] ?? []) : $attributes;
    }

    public function getVariableAttributes(string $property) : array
    {
        $reflectionProperty = new \ReflectionProperty(static::class, $property);
        $attributes = $reflectionProperty->getAttributes();

        return $attributes;
    }
}
