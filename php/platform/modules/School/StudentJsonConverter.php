<?php
namespace School;

require_once __DIR__.'/StudentArrayConverter.php';

/**
 * Generated from NGS DSL
 *
 * Converts an object of class School\Student into a JSON string and backwards via an array converter.
 *
 * @package School
 * @version 0.9.6a
 */
abstract class StudentJsonConverter
{/**
     * @param string Json representation of the object(s)
     *
     * @return array|\School\Student An object or an array of objects of type "School\Student"
     */
    public static function fromJson($item)
    {
        $obj = json_decode($item, true);

        return \NGS\Utils::isJsonArray($item)
            ? \School\StudentArrayConverter::fromArrayList($obj)
            : \School\StudentArrayConverter::fromArray($obj);
    }

    /**
     * @param array|\School\Student An object or an array of objects of type "School\Student"
     *
     * @return string Json representation of the object(s)
     */
    public static function toJson($item)
    {
        $arr = \School\StudentArrayConverter::toArray($item);
        if(is_array($item))
            return json_encode($arr);
        if(empty($arr))
            return '{}';
        return json_encode($arr);
    }
}