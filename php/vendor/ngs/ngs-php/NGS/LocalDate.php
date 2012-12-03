<?php
namespace NGS;

require_once(__DIR__.'/Utils.php');
require_once(__DIR__.'/Timestamp.php');

use NGS\Utils;

/**
 * Date object with timezone
 *
 * @property \DateTime $value Returns DateTime object
 */
class LocalDate
{
    const DEFAULT_TIMEZONE = 'UTC';
    const STRING_FORMAT = 'Y-m-d';
    const FALLBACK_FORMAT = 'Y-m-d\\TH:i:s';

    /**
     * @var \DateTime $value
     */
    protected $datetime;

    /**
     * Constructs a new LocalDate instance from microtime epoch
     *
     * @param int|float $utime numeric value with epoch time (with decimals representing milli & microseconds)
     * @param string $timezone string representation of a timezone
     *
     * @return \DateTime
     */
    private static function fromNumeric($utime, $timezone)
    {
        $strtime = sprintf('%.6f', $utime);

        $dt = \DateTime::createFromFormat('U.u', $strtime, new \DateTimeZone($timezone));
        if($dt === false) {
            throw new \InvalidArgumentException('Cannot initialize "NGS\\LocalDate". Input number was in invalid format: "'.$utime.'"');
        }

        $dt->setTimezone(new \DateTimeZone($timezone));
        return $dt;
    }

    /**
     * Constructs a new LocalDate instance from microtime epoch
     *
     * @param string $strtime string representation of the date
     * @param string $timezone string representation of a timezone
     * @param string $pattern format in which to parse the date
     *
     * @return \DateTime
     */
    private static function fromString($strtime, $timezone, $pattern)
    {
        $dt = \DateTime::createFromFormat($pattern, $strtime, new \DateTimeZone($timezone));
        if($dt === false) {
            if($pattern===self::STRING_FORMAT) {
                $dt = \DateTime::createFromFormat(self::FALLBACK_FORMAT, $strtime, new \DateTimeZone($timezone));
            }
            if($dt === false) {
                throw new \InvalidArgumentException('Cannot initialize "NGS\\LocalDate". Input string was in invalid format: "'.$strtime.'"');
            }
        }
        return $dt;
    }

    /**
     * Constructs a new LocalDate instance
     *
     * @param \DateTime|\NGS\DateTime|string|int|float|null $value Instance of \DateTime or \NGS\LocalDate, valid string format, date as int/float, or null for current time
     * @param string $timezone|null string representation of a timezone, null defaults to 'UTC'
     * @param string $pattern format in which to parse the date, defaults to 'Y-m-d\\TH:i:s.uP'
     */
    public function __construct($value = 'now', $pattern = self::STRING_FORMAT, $timezone = self::DEFAULT_TIMEZONE)
    {
        // current date
        if($value === 'now') {
            $value = microtime(true);
        }

        if($pattern === null) {
            $pattern = self::STRING_FORMAT;
        }

        if($value instanceof \DateTime) {
            $this->datetime = clone $value;
        }
        elseif($value instanceof \NGS\Timestamp) {
            $this->datetime = $value->toDateTime();
        }
        elseif($value instanceof \NGS\LocalDate) {
            $this->datetime = $value->toDateTime();
        }
        else if(is_int($value) || is_float($value)) {
            $this->datetime = self::fromNumeric($value, $timezone);
        }
        elseif(is_string($value)) {
            $this->datetime = self::fromString($value, $timezone, $pattern);
        }
        else {
            throw new \InvalidArgumentException('LocalDate cannot be constructed from type "'.Utils::getType($value).'", valid types are \NGS\LocalDate, \DateTime, string, int, float or null (for current date/time).');
        }

        if($this->datetime === null) {
            throw new \InvalidArgumentException('LocalDate could not be constructed from type "'.Utils::getType($value).'" with value: "'.$value.'"');
        }
    }

    public static function toArray(array $items)
    {
        try {
            foreach ($items as $key => &$val) {
                if($val === null) {
                    throw new \InvalidArgumentException('Null value found in provided array');
                }
                if(!$val instanceof \NGS\LocalDate) {
                    $val = new \NGS\LocalDate($val);
                }
            }
        }
        catch(\Exception $e) {
            throw new \InvalidArgumentException('Element at index '.$key.' could not be converted to LocalDate!', 42, $e);
        }
        return $items;
    }

    /**
     * Returns time in default format 'Y-m-d\\TH:i:s.uP'
     *
     * @return string formatted date with time zone
     */
    public function __toString()
    {
        return $this->format(self::STRING_FORMAT);
    }

    /**
     * Returns time in default format 'Y-m-d\\TH:i:s.uP'
     *
     * @return string formatted date with time zone
     */
    public function format($pattern)
    {
        return $this->datetime->format($pattern);
    }

    /**
     * Checks for equality against another LocalDate instance
     *
     * @param \NGS\LocalDate $other Instance of NGS\LocalDate
     */
    public function equals(\NGS\LocalDate $other)
    {
        return $this->datetime == $other->toDateTime();
    }

    /**
     * Gets time in Unix timestamp
     *
     * @return int Unix timestamp
     */
    public function toInt()
    {
        return $this->datetime->getTimestamp();
    }

    /**
     * Gets time value as Datetime instance
     *
     * @return \DateTime Time value as DateTime instance
     */
    public function toDateTime()
    {
        return clone $this->datetime;
    }

    /**
     * Gets as \NGS\Timestamp instance
     *
     * @return \NGS\Timestamp Time value as NGS\Timestamp instance
     */
    public function toTimestamp()
    {
        return new \NGS\Timestamp($this->datetime);
    }
}
