<?php

/*
 * This file is part of the fw4/skarabee-weblink library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skarabee\Weblink\Response;

use ArrayIterator;
use Skarabee\Weblink\Exception\InvalidPropertyException;
use JsonSerializable;

class ResponseObject implements JsonSerializable, ResponseObjectInterface
{
    /** @var array */
    protected $_data = [];

    /** @var array */
    private $_propertyIndex = [];

    public function __construct(object $data, array $arrays = [])
    {
        // Force arrays
        foreach ($arrays as $array) {
            if (is_string($array)) {
                if (isset($data->$array)) {
                    $data->$array = self::getFirstPropertyOfObject($data->$array) ?: [];
                } else {
                    $data->$array = [];
                }
            }
        }
        foreach ($data as $property => &$value) {
            if ($property === '_') {
                $property = 'Value';
            }
            $this->_propertyIndex[strtolower($property)] = $property;
            $this->_data[$property] = $this->parseValue($value, $arrays[$property] ?? []);
        }
    }

    /**
     * Recursively parse response data.
     *
     * @param mixed $value
     * @param string|null $property Name of the property to parse
     *
     * @return self
     */
    protected function parseValue($value, array $arrays)
    {
        if (is_object($value)) {
            return new self($value, $arrays);
        } elseif (is_array($value)) {
            $result = [];
            foreach ($value as &$subvalue) {
                $result[] = $this->parseValue($subvalue, $arrays);
            }
            return $result;
        } elseif ($value === 'UNDEFINED' || $value === -1) {
            return null;
        } elseif ($value === 'TRUE') {
            return true;
        } elseif ($value === 'FALSE') {
            return false;
        } elseif (is_string($value) && preg_match('/^(?:[1-9]\d{3}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1\d|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[1-9]\d(?:0[48]|[2468][048]|[13579][26])|(?:[2468][048]|[13579][26])00)-02-29)\s(?:[01]\d|2[0-3]):[0-5]\d:[0-5]\d(?:\.\d{1,9})?(?:Z|[+-][01]\d:[0-5]\d)?$/', $value)) {
            return new \DateTime($value);
        } else {
            return $value;
        }
    }

    /**
     * Get all properties of this object.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->_data;
    }

    public function __get(string $property)
    {
        $property = $this->normalizePropertyName($property);
        return $this->_data[$property] ?? null;
    }

    public function __set(string $property, $value)
    {
        $this->_propertyIndex[strtolower($property)] = $property;
        $this->_data[$property] = $value;
    }

    public function __isset(string $property): bool
    {
        $index = strtolower($property);
        return isset($this->_propertyIndex[$index]);
    }

    public function __unset(string $property)
    {
        $property = $this->normalizePropertyName($property);
        unset($this->_data[$property]);
        unset($this->_propertyIndex[strtolower($property)]);
    }

    /**
     * @codeCoverageIgnore
     */
    public function __debugInfo()
    {
        return $this->getData();
    }

    protected function normalizePropertyName(string $property): string
    {
        $index = strtolower($property);
        if (empty($this->_propertyIndex[$index])) {
            throw new InvalidPropertyException($property . ' is not a valid property of ' . static::class);
        }
        return $this->_propertyIndex[$index];
    }

    /**
     * Returns the first property of an object, if it exists
     *
     * @return mixed
     */
    public static function getFirstPropertyOfObject(object $object)
    {
        $iterator = new ArrayIterator((array)$object);
        return $iterator->current();
    }

    /* JsonSerializable implementation */

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->getData();
    }
}
