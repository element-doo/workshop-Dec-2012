<?php
namespace School\Student;

require_once __DIR__.'/getShortPeople.php';

/**
 * Generated from NGS DSL
 *
 * Converts an object of class School\Student\getShortPeople into a simple array and backwards.
 *
 * @package School
 * @version 0.9.6a
 */
abstract class getShortPeopleArrayConverter
{/**
     * @param array|\School\Student\getShortPeople An object or an array of objects of type "School\Student\getShortPeople"
     *
     * @return array A simple array representation
     */
    public static function toArray($item)
    {
        if ($item instanceof \School\Student\getShortPeople)
            return self::toArrayObject($item);
        if (is_array($item))
            return self::toArrayList($item);

        throw new \InvalidArgumentException('Argument was not an instance of class "School\Student\getShortPeople" nor an array of said instances!');
    }

    private static function toArrayObject($item)
    {
        $ret = array();
        $ret['nameLimit'] = $item->nameLimit;
        return $ret;
    }

    private static function toArrayList(array $items)
    {
        $ret = array();

        foreach($items as $key => $val) {
            if (!$val instanceof \School\Student\getShortPeople)
                throw new \InvalidArgumentException('Element with index "'.$key.'" was not an object of class "School\Student\getShortPeople"! Type was: '.\NGS\Utils::getType($val));

            $ret[] = $val->toArray();
        }

        return $ret;
    }

    public static function fromArray($item)
    {
        if ($item instanceof \School\Student\getShortPeople)
            return $item;
        if (is_array($item))
            return new \School\Student\getShortPeople($item);

        throw new \InvalidArgumentException('Argument was not an instance of class "School\Student\getShortPeople" nor an array of said instances!');
    }

    public static function fromArrayList(array $items)
    {
        try {
            foreach($items as $key => &$val) {
                if($val === null)
                    throw new InvalidArgumentException('Null value found in provided array');
                if(!$val instanceof \School\Student\getShortPeople)
                    $val = new \School\Student\getShortPeople($val);
            }
        }
        catch (\Exception $e) {
            throw new \InvalidArgumentException('Element at index '.$key.' could not be converted to object "School\Student\getShortPeople"!', 42, $e);
        }

        return $items;
    }
}