<?php

use NGS\BigDecimal;

/**
 * Test constructors
 */
class BigDecimalTest extends PHPUnit_Framework_TestCase
{
    public static function providerValues()
    {
        return array(
            array('12', 4),
            array(12, 1),
            array(0, 0),
            array(-13, 7),
            array('12.24', 35),
            array('-121212412.24', 2),
        );
    }

    public static function providerInvalid()
    {
        return array(
            array('12.14.42'),
            array(array()),
            array(true),
            array(false),
            array(null),
        );
    }

    /**
     * @dataProvider providerValues
     */
    public function testConstruct($value, $precision)
    {
        $foo = new BigDecimal($value, $precision);
    }

    public function testConstructFromInstance()
    {
        $foo = new BigDecimal('1.23', 2);
        $this->assertSame('1.23', (string) $foo);

        $foo = new BigDecimal(new BigDecimal(1.25, 3));
        $this->assertSame('1.250', (string) $foo);

        $foo = new BigDecimal(new BigDecimal(4, 0));
        $this->assertSame('4', (string) $foo);

        $foo = new BigDecimal(new BigDecimal(4, 0));
        $this->assertSame('4', (string) $foo);
    }

    /**
     * @dataProvider providerInvalid
     * @expectedException InvalidArgumentException
     */
    public function testInvalid($value)
    {
        $foo = new BigDecimal($value);
    }

    public function testAdd()
    {
        $foo = new BigDecimal('1.00', 2);
        $foo = $foo->add($foo);

        $this->assertSame('2.00', (string) $foo);
        $this->assertSame('1.75', (string) $foo->sub('0.25'));
        $this->assertSame('1.75', (string) $foo->sub(new BigDecimal('0.25123124124', 2)));

        $this->assertSame('1.00', (string) $foo->sub(1));
        $this->assertSame('8.00', (string) $foo->mul(4));
    }
}
