<?php
namespace School;

require_once __DIR__.'/DemographicArrayConverter.php';

/**
 * Generated from NGS DSL
 *
 * Converts an object of class School\Demographic into a JSON string and backwards via an array converter.
 *
 * @package School
 * @version 0.9.6a
 */
abstract class DemographicJsonConverter
{/**
     * @param string Json representation of the object(s)
     *
     * @return array|\School\Demographic An object or an array of objects of type "School\Demographic"
     */
    public static function fromJson($item)
    {
        $obj = json_decode($item, true);

        return \NGS\Utils::isJsonArray($item)
            ? \School\DemographicArrayConverter::fromArrayList($obj)
            : \School\DemographicArrayConverter::fromArray($obj);
    }

    /**
     * @param array|\School\Demographic An object or an array of objects of type "School\Demographic"
     *
     * @return string Json representation of the object(s)
     */
    public static function toJson($item)
    {
        $arr = \School\DemographicArrayConverter::toArray($item);
        if(is_array($item))
            return json_encode($arr);
        if(empty($arr))
            return '{}';
        return json_encode($arr);
    }
}