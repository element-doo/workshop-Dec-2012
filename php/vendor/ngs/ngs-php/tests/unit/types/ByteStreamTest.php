<?php

use NGS\ByteStream;

/**
 * Test constructors
 */
class ByteStreamTest extends PHPUnit_Framework_TestCase
{
    static $provider = null;

    public static function instance()
    {
        return new ByteStream();
    }

    public static function providerString()
    {
        return array(
            array(''),
            array('abc'),
            array('ĐŠL;ĆČĆK=#ŠIJĆČĆ:'),
        );
    }

    public static function providerInvalidValues()
    {
        return array(
            array( true ),
            array( false ),
            array( array() ),
            array( new stdClass() )
        );
    }

    /**
     * @dataProvider providerString
     */
    public function testConstruct($str)
    {
        $foo = new ByteStream($str);
        $bar = new ByteStream($foo);

        $this->assertEquals($foo, $bar);
    }

    /**
     * @expectedException InvalidArgumentException
     * @dataProvider providerInvalidValues
     */
    public function testConstructFromInvalidType($invalidValue)
    {
        new ByteStream($invalidValue);
    }

    /**
     * @dataProvider providerString
     */
    public function testEquals($str)
    {
        $foo = new ByteStream($str);
        $bar = new ByteStream($str);

        $this->assertTrue($foo->equals($bar));
        $this->assertNotSame($foo, $bar);
    }

    /**
     * @dataProvider providerString
     */
    public function testBase64($str)
    {
        $foo = new ByteStream($str);
        $this->assertSame(base64_encode($str), $foo->toBase64());
    }

}
