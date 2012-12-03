<?php
namespace NGS;

abstract class Utils
{
    private static $warningsAsErrors;

    public static function getType($var)
    {
        // native gettype
        $type = \gettype($var);

        if($type === 'object')
            return get_class($var);
        if($type === 'resource')
            return get_resource_type($var);
        return $type;
    }

    public static function WarningsAsErrors($wae = null)
    {
        if($wae === null)
            return self::$warningsAsErrors;
        self::$warningsAsErrors = (bool)$wae;
    }

    // if a json string starts with a '[' then it represents an array
    public static function isJsonArray($json)
    {
        return ord(ltrim($json)) === 91;
    }

    public static function toStringArray(array $list)
    {
        $res = array();

        foreach($list as $key => $val) {
            $res[$key] = $val->__toString();
        }

        return $res;
    }
}
