<?php

/**
 * Test methods
 */
class MethodsTest extends PHPUnit_Framework_TestCase
{

    public function providerInstance()
    {
        return array(
            array(
                new Img\Icon(array(
                    'code'          => 42,
                    'description'   => 'php rulz',
                    'bitmask'       => array(true, false, true, false),
                    'polygon'       => array(1.3, 2.1234, 3.14) )
                )
            ),
            array(
                new Img\Icon(array(
                    'code'          => 0,
                    'description'   => null,
                    'bitmask'       => null,
                    'polygon'       => array())
                )
            ),
            array(
                new Img\Icon(array(
                    'code'          => 0,
                    'description'   => '',
                    'bitmask'       => array() )
                )
            )
        );
    }

    /**
     * @dataProvider providerInstance
     * @expectedException InvalidArgumentException
     */
    public function testSetInvalidType(Img\Icon $icon)
    {
        $icon->description = true;
    }

    /**
     * @dataProvider providerInstance
     * @expectedException InvalidArgumentException
     */
    public function testSetNonNullToNull(Img\Icon $icon)
    {
        $icon->code = null;
    }

    /**
     * @dataProvider providerInstance
     * @expectedException InvalidArgumentException
     */
    public function testSetInvalidTypeInArray(Img\Icon $icon)
    {
        $icon->bitmask = array(true, false, "this is not a bool");
    }

    /**
     * @dataProvider providerInstance
     */
    public function testSetters(Img\Icon $icon)
    {
        $icon->description = 'bla';
        $icon->code = 123;
        $icon->bitmask = array(false, false, true);

        $this->assertSame('bla', $icon->description);
        $this->assertSame(123, $icon->code);
        $this->assertSame(array(false, false, true), $icon->bitmask);
    }

    /**
     * @dataProvider providerInstance
     */
    public function testToString(Img\Icon $icon)
    {
       // $this->expectOutputString('');
       // echo (string) $icon;

        $this->assertSame((string) $icon, (string) $icon);
    }

    public function testString()
    {
        //$this->assertSame('array', (string) array());
    }

    /**
     * @dataProvider providerInstance
     */
    public function testUnsetNullableProperty(Img\Icon $icon)
    {
        unset($icon->description);
        unset($icon->bitmask);


        $this->assertSame(null, $icon->description);
        $this->assertSame(null, $icon->bitmask);
    }

    /**
     * @dataProvider providerInstance
     * @expectedException LogicException
     */
    public function testUnsetNotNullProperty(Img\Icon $icon)
    {
        unset($icon->code);
    }

    /**
     * @dataProvider providerInstance
     */
    public function testIsset(Img\Icon $icon)
    {
        $this->assertSame(true, isset($icon->code));

        $this->assertSame(false, isset($icon->non_existing_property));

        $icon->description = null;
        $this->assertSame(false, isset($icon->description));
    }

    /**
     * @dataProvider providerInstance
     */
    public function testClone(Img\Icon $icon)
    {
        $cloned = clone $icon;
        $this->assertNotSame($cloned, $icon);



        // @todo test object properties
        // $this->assertNotSame($cloned->some_object, $icon->some_object);
    }

}
