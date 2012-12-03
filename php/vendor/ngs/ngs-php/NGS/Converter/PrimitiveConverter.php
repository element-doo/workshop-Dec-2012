<?php
namespace NGS\Converter;

require_once(__DIR__.'/../Utils.php');

use InvalidArgumentException;
use NGS\Utils;

/**
 * Converts values to primitive php types
 */
abstract class PrimitiveConverter
{
    public static function toInteger($value)
    {
        $result = filter_var($value, FILTER_VALIDATE_INT);
        if($result === false) {
            throw new InvalidArgumentException('Could not convert value '.$value.' of type "'.Utils::getType($value).'" to integer!');
        }
        return $result;
    }

    public static function toIntegerArray(array $items)
    {
        try {
            foreach ($items as $key => &$val) {
                if($val === null) {
                    throw new InvalidArgumentException('Null value found in provided array');
                }
                $val = self::toInteger($val);
            }
        }
        catch(\Exception $e) {
            throw new \InvalidArgumentException('Element at index '.$key.' could not be converted to integer!', 42, $e);
        }
        return $items;
    }

    public static function toString($value)
    {
        if(is_string($value))
            return $value;
        if(is_int($value) || is_float($value))
            return (string) $value;
        throw new InvalidArgumentException('Could not convert value '.$value.' of type "'.Utils::getType($value).'" to string!');
    }

    public static function toStringArray(array $items)
    {
        try {
            foreach ($items as $key => &$val) {
                if($val === null) {
                    throw new InvalidArgumentException('Null value found in provided array');
                }
                $val = self::toString($val);
            }
        }
        catch(\Exception $e) {
            throw new \InvalidArgumentException('Element at index '.$key.' could not be converted to string!', 42, $e);
        }
        return $items;
    }

    /**
     * Converts strings 'true','1'/'false','0' (ignores case) and integers 0/1 to booleans
     */
    public static function toBoolean($value)
    {
        if(is_bool($value))
            return $value;
        if(is_string($value)) {
            $tmp = strtolower($value);
            if($value === 'true' || $value === 'on' || $value === '1')
                return true;
            if($value === 'false' || $value === 'off' || $value === '0' || $value === '')
                return false;
            throw new InvalidArgumentException('Could not convert value "'.$value.'" of type "string" to boolean!');
        }
        if(is_int($value)) {
            if($value === 1)
                return true;
            if($value === 0)
                return false;
            throw new InvalidArgumentException('Could not convert value '.$value.' of type "integer" to boolean!');
        }
        throw new InvalidArgumentException('Could not convert value '.$value.' of type "'.Utils::getType($value).'" to boolean!');
    }

    public static function toBooleanArray(array $items)
    {
        try {
            foreach ($items as $key => &$val) {
                if($val === null) {
                    throw new InvalidArgumentException('Null value found in provided array');
                }
                $val = self::toBoolean($val);
            }
        }
        catch(\Exception $e) {
            throw new \InvalidArgumentException('Element at index '.$key.' could not be converted to boolean!', 42, $e);
        }
        return $items;
    }

    public static function toFloat($value)
    {
        $result = filter_var($value, FILTER_VALIDATE_FLOAT);
        if($result === false){
            throw new InvalidArgumentException('Could not convert value '.$value.' of type "'.Utils::getType($value).'" to float!');
        }
        return $result;
    }

    public static function toFloatArray(array $items)
    {
        try {
            foreach ($items as $key => &$val) {
                if($val === null) {
                    throw new InvalidArgumentException('Null value found in provided array');
                }
                $val = self::toFloat($val);
            }
        }
        catch(\Exception $e) {
            throw new \InvalidArgumentException('Element at index '.$key.' could not be converted to float!', 42, $e);
        }
        return $items;
    }

    public static function toMap(array $value)
    {
        try {
            foreach ($value as $key => &$val) {
                if($val === null) {
                    throw new InvalidArgumentException('Null value found in provided array');
                }

                $val = self::toString($val);
            }
        }
        catch(\Exception $e) {
            throw new \InvalidArgumentException('Element '.$key.' could not be converted to string!', 42, $e);
        }
        return $value;
    }

    public static function toMapArray(array $items)
    {
        try {
            foreach ($items as $key => &$val) {
                if($val === null) {
                    throw new InvalidArgumentException('Null value found in provided array');
                }
                $val = self::toMap($val);
            }
        }
        catch(\Exception $e) {
            throw new \InvalidArgumentException('Element at index '.$key.' could not be converted to array[string, string]!', 42, $e);
        }
        return $items;
    }
}
