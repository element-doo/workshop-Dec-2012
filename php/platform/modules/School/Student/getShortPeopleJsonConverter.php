<?php
namespace School\Student;

require_once __DIR__.'/getShortPeopleArrayConverter.php';

/**
 * Generated from NGS DSL
 *
 * Converts an object of class School\Student\getShortPeople into a JSON string and backwards via an array converter.
 *
 * @package School
 * @version 0.9.6a
 */
abstract class getShortPeopleJsonConverter
{/**
     * @param string Json representation of the object(s)
     *
     * @return array|\School\Student\getShortPeople An object or an array of objects of type "School\Student\getShortPeople"
     */
    public static function fromJson($item)
    {
        $obj = json_decode($item, true);

        return \NGS\Utils::isJsonArray($item)
            ? \School\Student\getShortPeopleArrayConverter::fromArrayList($obj)
            : \School\Student\getShortPeopleArrayConverter::fromArray($obj);
    }

    /**
     * @param array|\School\Student\getShortPeople An object or an array of objects of type "School\Student\getShortPeople"
     *
     * @return string Json representation of the object(s)
     */
    public static function toJson($item)
    {
        $arr = \School\Student\getShortPeopleArrayConverter::toArray($item);
        if(is_array($item))
            return json_encode($arr);
        if(empty($arr))
            return '{}';
        return json_encode($arr);
    }
}