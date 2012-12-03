<?php
namespace NGS\ModelBundle\Util;

abstract class AggregateCache
{
    const CACHE_PREFIX = 'NGS_';

    private static function getCachedName($name)
    {
        // strip '\' from name, so there is no confusion with starting slash
        //  when caching class names, example: '\NGS\MyClass' vs 'NGS\MyClass'
        return self::CACHE_PREFIX.str_replace('\\', '', $name);
    }

    public static function save($name, array $items)
    {
        apc_store(self::getCachedName($name), $items);
    }

    public static function load($name)
    {
        return apc_fetch(self::getCachedName($name), $ok);
    }
}