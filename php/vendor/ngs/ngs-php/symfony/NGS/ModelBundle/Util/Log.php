<?php
namespace NGS\ModelBundle\Util;

// helper static class for firebug loging
abstract class Log
{
    private static $logger;

    public static function setLogger($loggerInstance)
    {
        self::$logger = $loggerInstance;
    }

    public static function info($message)
    {
        self::$logger->info($message);
    }

    public static function warning($message)
    {
        self::$logger->addWarning($message);
    }

    public static function error($message)
    {
        self::$logger->error($message);
    }
}