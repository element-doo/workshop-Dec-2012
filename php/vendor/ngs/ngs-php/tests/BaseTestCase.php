<?php
use NGS\Client\StandardProxy;

abstract class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    protected function deleteAll($class)
    {
        $items = $class::findAll();
        $proxy = new StandardProxy();
        $proxy->delete($items); 
    }
}
