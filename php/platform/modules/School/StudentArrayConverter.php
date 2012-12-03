<?php
namespace School;

require_once __DIR__.'/Student.php';

/**
 * Generated from NGS DSL
 *
 * Converts an object of class School\Student into a simple array and backwards.
 *
 * @package School
 * @version 0.9.6a
 */
abstract class StudentArrayConverter
{/**
     * @param array|\School\Student An object or an array of objects of type "School\Student"
     *
     * @return array A simple array representation
     */
    public static function toArray($item)
    {
        if ($item instanceof \School\Student)
            return self::toArrayObject($item);
        if (is_array($item))
            return self::toArrayList($item);

        throw new \InvalidArgumentException('Argument was not an instance of class "School\Student" nor an array of said instances!');
    }

    private static function toArrayObject($item)
    {
        $ret = array();
        $ret['URI'] = $item->URI;
        $ret['ID'] = $item->ID;
        $ret['firstName'] = $item->firstName;
        $ret['lastName'] = $item->lastName;
        $ret['name'] = $item->name;
        $ret['birthdate'] = $item->birthdate->__toString();
        return $ret;
    }

    private static function toArrayList(array $items)
    {
        $ret = array();

        foreach($items as $key => $val) {
            if (!$val instanceof \School\Student)
                throw new \InvalidArgumentException('Element with index "'.$key.'" was not an object of class "School\Student"! Type was: '.\NGS\Utils::getType($val));

            $ret[] = $val->toArray();
        }

        return $ret;
    }

    public static function fromArray($item)
    {
        if ($item instanceof \School\Student)
            return $item;
        if (is_array($item))
            return new \School\Student($item, 'build_internal');

        throw new \InvalidArgumentException('Argument was not an instance of class "School\Student" nor an array of said instances!');
    }

    public static function fromArrayList(array $items)
    {
        try {
            foreach($items as $key => &$val) {
                if($val === null)
                    throw new InvalidArgumentException('Null value found in provided array');
                if(!$val instanceof \School\Student)
                    $val = new \School\Student($val, 'build_internal');
            }
        }
        catch (\Exception $e) {
            throw new \InvalidArgumentException('Element at index '.$key.' could not be converted to object "School\Student"!', 42, $e);
        }

        return $items;
    }
}