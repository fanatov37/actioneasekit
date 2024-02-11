<?php

namespace ActionEaseKit\Base\Entity;

use Doctrine\ORM\Mapping as ORM;
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
    public const DEFAULT_PROPERTY_NAME = 'data';

    /** @ORM\Column(name="data", type="json", nullable=true, options={"jsonb": true}) */
    protected $data = [];

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setIndicator(
        string|array $objectPath,
        mixed $value,
        bool $skipCheckObjectPath=false,
        string $propertyName = self::DEFAULT_PROPERTY_NAME
    ) : self
    {
        $this->checkProperty($propertyName);

        if (is_array($objectPath)) {
            $objectPath = implode(':', $objectPath);
        }

        if (!$skipCheckObjectPath) {
            $this->checkIndicator($objectPath, $propertyName);
        }

        self::set($this->{$propertyName}, $objectPath, $value);

        return $this;
    }

    public function getIndicator(
        string|array $objectPath,
        mixed $default = null,
        bool $skipCheckObjectPath=false,
        string $propertyName = self::DEFAULT_PROPERTY_NAME
    ) : mixed
    {
        $this->checkProperty($propertyName);

        if (is_array($objectPath)) {
            $objectPath = implode(':', $objectPath);
        }

        if (!$skipCheckObjectPath) {
            $this->checkIndicator($objectPath, $propertyName);
        }

        $result = self::get((array)$this->{$propertyName}, $objectPath, $default);

        return $result;
    }

    protected function checkProperty(string $propertyName)
    {
        isset($this->{$propertyName}) ?? throw new \Exception("Property {$propertyName} not exists");
    }

    protected function checkIndicator(string $objectPath, string $propertyName)
    {
        $indicatorConstName = $this::class . '::INDICATOR_' . strtoupper($propertyName);
        defined($indicatorConstName) ?? throw new \Exception("Indicator not exists");

        $indicators = constant($indicatorConstName);

        if (!in_array($objectPath, self::arrayToColumn($indicators))) {
            throw new \Exception("Property {$objectPath} is not accepted for entity " . $this::class);
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

    protected static function set(&$array, $key, $value)
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
