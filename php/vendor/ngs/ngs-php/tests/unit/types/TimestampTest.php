<?php

use \NGS\Timestamp;

/**
 * Test constructors
 */
class TimestampTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        // date_default_timezone_set(LocalDate::DATETIMEZONE);
    }

    public static function providerDates()
    {
        return array(
            array('2012-07-20T13:33:25.869343+00:00', 'Y-m-d\\TH:i:s.uP'),
            array('1012-11-30', 'Y-m-d'),
            array('0001', 'Y'),
            array('0001-01-01 12:15', 'Y-m-d H:i'),
            array('31.12.1999', 'd.m.Y', 'CET')
        );
    }

    /**
     * @dataProvider providerDates
     */
    public function testConstructFromString($date, $format)
    {
        new Timestamp($date, $format);
    }

    public function testFormat()
    {
        $format = 'Y-m-d H:i';
        $date = date('Y-m-d H:i');
        $ts = new Timestamp($date, $format);

        $this->assertSame($date, $ts->format($format));
        $this->assertSame((string) $ts, $ts->format(Timestamp::STRING_FORMAT));
    }

    /**
     * @dataProvider providerDates
     */
    public function testConstructFromDateTime($value, $format, $timezone=Timestamp::DEFAULT_TIMEZONE)
    {
        $dt = \DateTime::createFromFormat($format, $value, new \DateTimeZone($timezone));
        $ts = new Timestamp($value, $format, $timezone);

        $this->assertEquals($dt, $ts->toDateTime());
    }

    /**
     * @dataProvider providerDates
     */
    public function testConstructFromLocalDate($dateString, $format)
    {
        $foo = new \NGS\LocalDate($dateString, $format);
        $bar = new Timestamp($foo);
        $this->assertSame($foo->format('Y-m-d'), $bar->format('Y-m-d'));
    }

    public function testEquals()
    {
        $foo = new Timestamp('2012-01-05', 'Y-m-d');
        $bar = new Timestamp('2012-01-05', 'Y-m-d');
        $bar2 = new Timestamp('2012-01-06', 'Y-m-d');

        $this->assertTrue($foo->equals($bar));
        $this->assertFalse($foo->equals($bar2));
    }

    public function testToInt()
    {
        $time = time();
        $ts = new Timestamp($time);
        $this->assertSame($time, $ts->toInt());
    }

    public function testToFloat()
    {
        $time = 123456.78912;
        $ts = new Timestamp($time);
        $this->assertSame($time, $ts->toFloat());
    }

    public function testToDateTime()
    {
        $ts = new Timestamp();
        $datetime = $ts->toDateTime();
        $this->assertTrue($ts->toDateTime() instanceof \DateTime);

        $this->assertEquals($ts, new Timestamp($datetime));
    }
}
