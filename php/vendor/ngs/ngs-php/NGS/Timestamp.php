<?php
namespace NGS;

require_once(__DIR__.'/Utils.php');
require_once(__DIR__.'/LocalDate.php');

use NGS\Utils;

/**
 * Date object with timezone
 *
 * @property \DateTime $DateTime Returns DateTime object
 */
class Timestamp
{
    const DEFAULT_TIMEZONE = 'UTC';
    const STRING_FORMAT = 'Y-m-d\\TH:i:s.uP';
    // use this format if default format fails
    // added because soap uses both formats
    const FALLBACK_FORMAT = 'Y-m-d\\TH:i:sP';

    /**
     * @var \DateTime
     */
    protected $datetime;

    /**
     * Constructs a new Timestamp instance from microtime epoch
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
            throw new \InvalidArgumentException('Cannot initialize "NGS\\Timestamp". Input number was in invalid format: "'.$utime.'"');
        }

        $dt->setTimezone(new \DateTimeZone($timezone));
        return $dt;
    }

    /**
     * Constructs a new Timestamp instance from microtime epoch
     *
     * @param string $strtime string representation of the timestamp
     * @param string $timezone string representation of a timezone
     * @param string $pattern format in which to parse the timestamp
     *
     * @return \DateTime
     */
    private static function fromString($strtime, $timezone, $pattern)
    {
        $dt = \DateTime::createFromFormat($pattern, $strtime, new \DateTimeZone($timezone));
        if($dt === false) {
            // try fallback format
            $dt = \DateTime::createFromFormat(self::FALLBACK_FORMAT, $strtime, new \DateTimeZone($timezone));

            if($dt === false) {
                //let's try again
                $dt = date_parse($strtime);

                if($dt == false)
                    throw new \InvalidArgumentException('Cannot initialize "NGS\\Timestamp". Input string was in invalid format: "'.$strtime.'"');
            }
        }
        return $dt;
    }

    /**
     * Constructs a new Timestamp instance
     *
     * @param \DateTime|\NGS\DateTime|string|int|float|null $value Instance of \DateTime or \NGS\Timestamp, valid string format, timestamp as int/float, or null for current time
     * @param string $timezone string representation of a timezone, null defaults to 'UTC'
     * @param string $pattern format in which to parse the timestamp, defaults to 'Y-m-d\\TH:i:s.uP'
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
            throw new \InvalidArgumentException('Timestamp cannot be constructed from type "'.Utils::getType($value).'", valid types are \NGS\Timestamp, \DateTime, string, int, float or null (for current date/time).');
        }

        if($this->datetime === null) {
            throw new \InvalidArgumentException('Timestamp could not be constructed from type "'.Utils::getType($value).'" with value: "'.$value.'"');
        }
    }

    public static function toArray(array $items)
    {
        try {
            foreach ($items as $key => &$val) {
                if($val === null) {
                    throw new \InvalidArgumentException('Null value found in provided array');
                }
                if(!$val instanceof \NGS\Timestamp) {
                    $val = new \NGS\Timestamp($val);
                }
            }
        }
        catch(\Exception $e) {
            throw new \InvalidArgumentException('Element at index '.$key.' could not be converted to Timestamp!', 42, $e);
        }
        return $items;
    }

    /**
     * Returns time in default format 'Y-m-d\\TH:i:s.uP'
     *
     * @return string formatted timestamp with time zone
     */
    public function __toString()
    {
        return $this->format(self::STRING_FORMAT);
    }

    /**
     * Returns time in default format 'Y-m-d\\TH:i:s.uP'
     *
     * @return string formatted timestamp with time zone
     */
    public function format($pattern)
    {
        return $this->datetime->format($pattern);
    }

    /**
     * Checks for equality against another Timestamp instance
     *
     * @param \NGS\Timestamp $other Instance of NGS\Timestamp
     */
    public function equals(\NGS\Timestamp $other)
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
     * Gets time in Unix timestamp with microseconds
     *
     * @return float Unix timestamp with microseconds
     */
    public function toFloat()
    {
        return (float) $this->datetime->format('U.u');
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
     * Gets as \NGS\LocalDate instance
     *
     * @return \NGS\LocalDate Time value as NGS\LocalDate instance
     */
    public function toLocalDate()
    {
        return new \NGS\LocalDate($this->datetime);
    }
}
