<?php

use NGS\Money;

/**
 * Test constructors
 */
class MoneyTest extends PHPUnit_Framework_TestCase
{
    public static function providerValues()
    {
        return array(
            array('12'),
            array(12),
            array(0),
            array(-13),
            array('12.24'),
            array('-121212412.24'),
            array(new Money(1)),
        );
    }

    public static function providerInvalid()
    {
        return array(
            array('12.14.42'),
    /*        array(12.4235),
            array(array()),
            array(true),
            array(false),
            array(null),
            array('12.1'), */
        );
    }

    /**
     * @dataProvider providerValues
     */
    public function testConstruct($value)
    {
        $foo = new Money($value);
    }

    /**
     * @dataProvider providerInvalid
     * @expectedException InvalidArgumentException
     */
    public function testInvalid($value)
    {
        $foo = new Money($value);
    }
}
