<?php

use NGS\LocalDate;

/**
 * Test constructors
 */
class LocalDateTest extends PHPUnit_Framework_TestCase
{
    static $provider=null;

    protected function setUp()
    {
        date_default_timezone_set(LocalDate::DEFAULT_TIMEZONE);
    }

    public static function providerString()
    {
        return array(
            array('2012-05-21'),
            array('1012-11-30'),
            array('0001-01-01'),
            array('0001-01-01')
        );
    }

    public static function providerInvalidValues()
    {
        return array(
            array( true ),
            array( false ),
            array( array() ),
            array( new stdClass() ),
            array( 'aaa' ),
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @dataProvider providerInvalidValues
     */
    public function testConstructFromInvalidType($invalidValue)
    {
       $date = new LocalDate($invalidValue);
    }

    /**
     * @dataProvider providerString
     */
    public function testConstructFromString($dateString)
    {
        $date = new LocalDate($dateString);
        $this->assertSame($dateString, (string) $date);
    }

    public function testConstructFromNull()
    {
        $today = date(LocalDate::STRING_FORMAT);
        $date = new LocalDate();
        $this->assertSame($today, (string) $date);
    }

    /**
     * @dataProvider providerString
     */
    public function testConstructFromDatetime($dateString)
    {
        new \DateTime($dateString, new \DateTimeZone(LocalDate::DEFAULT_TIMEZONE));
        $date = new LocalDate($dateString);
        $this->assertSame($dateString, (string) $date);
    }

    /**
     * @dataProvider providerString
     */
    public function testConstructFromLocalDate($dateString)
    {
        $foo = new LocalDate($dateString);
        $bar = new LocalDate($foo);
        $this->assertEquals($foo, $bar);
        $this->assertNotSame($foo, $bar);
    }

    /**
     * @dataProvider providerString
     */
    public function testConstructFromTimestamp($dateString)
    {
        $foo = new \NGS\Timestamp($dateString, 'Y-m-d');
        $bar = new LocalDate($foo);
        $this->assertSame($foo->format('Y-m-d'), $bar->format('Y-m-d'));
    }


    public function testConstructFromNumber()
    {
        $date = '2012-12-31';
        $time = strtotime('2012-12-31');

        $foo = new LocalDate($time);
        $this->assertSame($date, (string) $foo);

        $bar = new LocalDate((float)($time.'.123456'));
        $this->assertSame($date, (string) $bar);
    }

    /**
     * @dataProvider providerString
     */
    public function testClone($dateString)
    {
        $foo = new LocalDate($dateString);
        $bar = clone $foo;
        $this->assertEquals($foo, $bar);
        $this->assertNotSame($foo, $bar);
    }

    public function testToInt()
    {
        $time = time();
        $ts = new LocalDate($time);
        $this->assertSame($time, $ts->toInt());
    }

    public function testToDateTime()
    {
        $ts = new LocalDate();
        $datetime = $ts->toDateTime();
        $this->assertTrue($ts->toDateTime() instanceof \DateTime);

        $this->assertEquals($ts, new LocalDate($datetime));
    }

    public function testConvertJson()
    {
        $dateValue = '2001-12-25';
        $date = new LocalDate($dateValue);

        $this->assertSame($dateValue, (string) $date);

        $values = array();
        $values['date'] = NGS\Converter\LocalDateConverter::toJson($date);

        $decoded = json_decode(json_encode($values));

        $dateFromJson = NGS\Converter\LocalDateConverter::fromJson($decoded->date);

        $this->assertEquals($date, $dateFromJson);
    }

    /**
     * @dataProvider providerString
     */
    public function testConvertToTimestamp($val)
    {
        $date = new LocalDate($val);
        $timestamp = $date->toTimestamp();
        $this->assertEquals($date, $timestamp->toLocalDate());
    }
}
