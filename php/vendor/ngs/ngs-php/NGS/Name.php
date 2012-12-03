<?php
namespace NGS;

/**
 * Helper functions for converting PHP class names to DSL names
 */
abstract class Name
{
    public static function full($name)
    {
        if (is_object($name)) {
            $name = get_class($name);
        }
        elseif(!is_string($name)) {
            throw new \InvalidArgumentException('Invalid type for name, name was not string');
        }
        return str_replace('\\', '.', $name);
    }

    public static function base($name)
    {
        $names = explode('.', self::full($name));
        return array_pop($names);
    }

    public static function toClass($name)
    {
        if (is_object($name)) {
            return get_class($name);
        }
        elseif (is_string($name)) {
            return str_replace('.', '\\', $name);
        }
        else {
            throw new \InvalidArgumentException('Invalid type for object name');
        }
    }

    public static function parent($name)
    {
        $names = explode('.', self::full($name));
        array_pop($names);
        return implode('.', $names);
    }
}
