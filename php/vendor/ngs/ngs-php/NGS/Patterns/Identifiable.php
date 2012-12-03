<?php
namespace NGS\Patterns;

require_once(__DIR__.'/IIdentifiable.php');
require_once(__DIR__.'/../Client/DomainProxy.php');
require_once(__DIR__.'/../Client/CrudProxy.php');
require_once(__DIR__.'/../Converter/PrimitiveConverter.php');

use NGS\Converter\PrimitiveConverter;
use NGS\Client\DomainProxy;
use NGS\Client\CrudProxy;

// print_r(get_lo);

abstract class Identifiable implements IIdentifiable
{
    /**
     * Finds objects by one or more URIs
     * @param string or string array of URIs
     * @return object or array of found objects
     */
    public static function find($uri)
    {
        if(is_array($uri)) {
            $uri = PrimitiveConverter::toStringArray($uri);
            return DomainProxy::instance()->find(get_called_class(), $uri);
        }
        $uri = PrimitiveConverter::toString($uri);
        return CrudProxy::instance()->read(get_called_class(), $uri);
    }

    /**
     * Checks if object with $uri exists
     * @param string $uri Object URI
     * @return bool True/false if object was/wasn't found
     */
    public static function exists($uri)
    {
        $uri = PrimitiveConverter::toString($uri);
        $res = self::find(array($uri));
        return !empty($res);
    }
}
