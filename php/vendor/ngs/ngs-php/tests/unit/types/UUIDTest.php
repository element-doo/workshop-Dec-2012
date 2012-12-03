<?php

use NGS\UUID;

/**
 * Test constructors
 */
class UUIDTest extends PHPUnit_Framework_TestCase
{
    public static function providerUidsV4()
    {
        return array(
            array('6a531a5f-854e-4f76-91dc-7ea84c568f48'),
            array('c666a865-a0d0-43e5-b5c8-280cdfc80603'),
            array('a5ccc752-2f9f-4fba-8104-6c4fb3d8a5cf'),
        );
    }

    public function testConstructV4()
    {
        $foo = UUID::v4();
        $bar = new UUID($foo);

        $this->assertSame($foo->value, $bar->value);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalid()
    {
        $foo = UUID::v3('nevalja', 'a');
    }

    /**
     * @dataProvider providerUidsV4
     */
    public function testGenerateFromV4($str)
    {
        $foo = new UUID($str);

        $foo = UUID::v3($str, 'abc');
        $bar = UUID::v3($str, 'abc');

        $this->assertSame($foo->value, $bar->value);

        $foo = UUID::v5($str, 'mirko');
        $bar = UUID::v5($str, 'mirko');

        $this->assertSame($foo->value, $bar->value);

        $foo = UUID::v5($str, 'mirko');
        $bar = UUID::v5($str, 'slavko');

        $this->assertNotSame($foo->value, $bar->value);
    }
}
