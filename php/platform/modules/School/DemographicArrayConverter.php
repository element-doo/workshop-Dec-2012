<?php
namespace School;

require_once __DIR__.'/Demographic.php';

/**
 * Generated from NGS DSL
 *
 * Converts an object of class School\Demographic into a simple array and backwards.
 *
 * @package School
 * @version 0.9.6a
 */
abstract class DemographicArrayConverter
{/**
     * @param array|\School\Demographic An object or an array of objects of type "School\Demographic"
     *
     * @return array A simple array representation
     */
    public static function toArray($item)
    {
        if ($item instanceof \School\Demographic)
            return self::toArrayObject($item);
        if (is_array($item))
            return self::toArrayList($item);

        throw new \InvalidArgumentException('Argument was not an instance of class "School\Demographic" nor an array of said instances!');
    }

    private static function toArrayObject($item)
    {
        $ret = array();
        $ret['minors'] = $item->minors === null ? null : \School\StudentArrayConverter::toArray($item->minors);
        return $ret;
    }

    private static function toArrayList(array $items)
    {
        $ret = array();

        foreach($items as $key => $val) {
            if (!$val instanceof \School\Demographic)
                throw new \InvalidArgumentException('Element with index "'.$key.'" was not an object of class "School\Demographic"! Type was: '.\NGS\Utils::getType($val));

            $ret[] = $val->toArray();
        }

        return $ret;
    }

    public static function fromArray($item)
    {
        if ($item instanceof \School\Demographic)
            return $item;
        if (is_array($item))
            return new \School\Demographic($item);

        throw new \InvalidArgumentException('Argument was not an instance of class "School\Demographic" nor an array of said instances!');
    }

    public static function fromArrayList(array $items)
    {
        try {
            foreach($items as $key => &$val) {
                if($val === null)
                    throw new InvalidArgumentException('Null value found in provided array');
                if(!$val instanceof \School\Demographic)
                    $val = new \School\Demographic($val);
            }
        }
        catch (\Exception $e) {
            throw new \InvalidArgumentException('Element at index '.$key.' could not be converted to object "School\Demographic"!', 42, $e);
        }

        return $items;
    }
}