<?php

namespace ActionEaseKit\Base\Entity;

/**
 * This class for quick get,set data for json columns. Just extend this class.
 *
 * for example, you have multiple columns jsonb data. Create const INDICATOR_{COLUMN_NAME}
 * add all data which can be in json (or use skip param for no check).
 *
 * and Just use getIndicator, setIndicator for quick access to your data in json.
 *
 * data column will be by default.
 *
 */
class IndicatorEntity
{
    const DEFAULT_PROPERTY_NAME = 'data';

    protected string $currentPropertyName = self::DEFAULT_PROPERTY_NAME;

    protected function setCurrentPropertyName(string $propertyName) : void
    {
        if (!property_exists($this, $propertyName)) {
            throw new \Exception("Property {$propertyName} does not exist");
        }

        $this->currentPropertyName = $propertyName;
    }

    /** full set json to current property without check */
    final public function setFull(array $data): self
    {
        $this->{$this->currentPropertyName} = $data;

        return $this;
    }

    final public function getFull(): array
    {
        return $this->{$this->currentPropertyName};
    }

    /**
     * $objectPath can be like array ['path1', 'path2'] or string path1:path2
     */
    final public function setIndicator(
        string|array $objectPath,
        mixed $value,
        bool $skipCheckObjectPath=false,
    ) : self
    {
        $this->checkProperty();

        if (is_array($objectPath)) {
            $objectPath = implode(':', $objectPath);
        }

        if (!$skipCheckObjectPath) {
            $this->checkIndicator($objectPath);
        }

        self::set($this->{$this->currentPropertyName}, $objectPath, $value);

        return $this;
    }

    /**
     * $objectPath can be like array ['path1', 'path2'] or string path1:path2
     */
    final public function getIndicator(
        string|array $objectPath,
        mixed $default = null,
        bool $skipCheckObjectPath=false,
    ) : mixed
    {
        $this->checkProperty();

        if (is_array($objectPath)) {
            $objectPath = implode(':', $objectPath);
        }

        if (!$skipCheckObjectPath) {
            $this->checkIndicator($objectPath);
        }

        $result = self::get((array)$this->{$this->currentPropertyName}, $objectPath, $default);

        return $result;
    }

    private function checkProperty() : void
    {
        isset($this->{$this->currentPropertyName}) ??
            throw new \Exception("Property {$this->currentPropertyName} not exists");
    }

    private function checkIndicator(string $objectPath) : void
    {
        $indicatorConstName = static::class . '::INDICATOR_' . strtoupper($this->currentPropertyName);
        defined($indicatorConstName) ?? throw new \Exception("Indicator not exists");

        $indicators = constant($indicatorConstName);

        if (!in_array($objectPath, self::arrayToColumn($indicators))) {
            throw new \Exception("Property {$objectPath} is not accepted for entity " . static::class);
        }
    }

    protected static function arrayToColumn(array $data): array
    {
        $result = [];

        foreach ($data as $key => $el) {
            if (is_string($el) || is_numeric($el)) {
                $result[] = $el;
            } elseif (is_array($el)) {
                $result[] = $key;

                foreach (self::arrayToColumn($el) as $item) {
                    $result[] = $key.':'.$item;
                }
            }
        }

        return $result;
    }

    protected static function get(array $array, string $key, mixed $default = null) : mixed
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (strpos($key, ':') === false) {
            return $default;
        }

        foreach (explode(':', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    protected static function set(&$array, $key, $value) : void
    {
        $keys = explode(':', $key);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        $array[array_shift($keys)] = $value;
    }
}
