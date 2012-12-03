<?php
namespace NGS;

require_once(__DIR__.'/Utils.php');

use NGS\Utils;

/**
 *
 * @property string $value String representation of decimal value
 * @property int $precision Precision for this instance
 */
class BigDecimal
{
    // used for new objects, if no precision is explicitly set
    const DEFAULT_PRECISION = 20;

    /** @var string String representation of decimal value */
    protected $value;

    /** @var int Precision */
    protected $precision;

    /**
     *
     * @params int|string|\NGS\BigDecimal Decimal value or BigDecimal instance
     * @param int $precision Decimal precision, can be null if constructin from another BigDecimal
     */
    public function __construct($value=0, $precision=self::DEFAULT_PRECISION)
    {
        // construct from anoter BigDecimal - don't need precision
        if ($value instanceof \NGS\BigDecimal) {
            $this->value = $value->value;
            $this->precision = $value->precision;
        }
        else {
            $this->setPrecision($precision);
            $this->setValue($value);
        }
    }

    public static function toArray(array $items)
    {
        try {
            foreach ($items as $key => &$val) {
                if($val === null) {
                    throw new \InvalidArgumentException('Null value found in provided array');
                }
                if(!$val instanceof \NGS\BigDecimal) {
                    $val = new \NGS\BigDecimal($val);
                }
            }
        }
        catch(\Exception $e) {
            throw new \InvalidArgumentException('Element at index '.$key.' could not be converted to BigDecimal!', 42, $e);
        }
        return $items;
    }

    /**
     * @param int $precision
     */
    private function setPrecision($precision)
    {
        if (!is_int($precision)) {
            throw new \InvalidArgumentException('BigDecimal precison was not int, type was: "'.Utils::getType($precision).'"');
        }
        elseif ($precision<0) {
            throw new \InvalidArgumentException('BigDecimal precison cannot be negative value: "'.Utils::getType($precision).'"');
        }
        $this->precision = $precision;
    }

    /**
     * @param string|int|float $value
     */
    private function setValue($value)
    {
        if ($value === null) {
            throw new \InvalidArgumentException('BigDecimal value cannot be null');
        }
        elseif (is_string($value)) {
            if (!filter_var($value, FILTER_VALIDATE_INT)
                && !preg_match('/^[-+]?\\d+([.]\\d+)?$/u', $value)) {
                throw new \InvalidArgumentException('Invalid characters in BigDecimal constructor string: '.$value);
            }
            $this->value = bcadd($value, 0, $this->precision);
        }
        elseif (is_int($value)) {
            $this->value = bcadd($value, 0, $this->precision);
        }
        elseif (is_float($value)) {
            $this->value = bcadd($value, 0, $this->precision);
        }
        else {
            throw new \InvalidArgumentException('Invalid type for BigDecimal value, type was: "'.Utils::getType($value).'"');
        }
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __get($name)
    {
        if($name==='value') {
            return $this->value;
        }
        else if($name==='precision') {
            return $this->precision;
        }
        else {
            throw new \InvalidArgumentException('BigDecimal: Cannot get undefined property "'.$name.'"');
        }
    }

    public function __toString()
    {
        return $this->value;
    }

// ----------------------------------------------------------------------------

    protected function _add(BigDecimal $other)
    {
        return new BigDecimal(bcadd($this->value, $other->value, $this->precision), $this->precision);
    }

    public function add($other)
    {
        return $this->_add(new BigDecimal($other, $this->precision));
    }

    protected function _sub(BigDecimal $other)
    {
        return new BigDecimal(bcsub($this->value, $other->value, $this->precision), $this->precision);
    }

    public function sub($other)
    {
        return $this->_sub(new BigDecimal($other, $this->precision));
    }

// ----------------------------------------------------------------------------

    protected function _comp(BigDecimal $other)
    {
        return bccomp($this->value, $other->value, $this->precision);
    }

    public function comp($other)
    {
        return $this->_comp(new BigDecimal($other, $this->precision));
    }

    protected function _gt(BigDecimal $other)
    {
        return $this->comp($other) > 0;
    }

    public function gt($other)
    {
        return $this->_gt(new BigDecimal($other, $this->precision));
    }

    protected function _gte(BigDecimal $other) {
        return $this->comp($other) >= 0;
    }

    public function gte($other)
    {
        return $this->_gte(new BigDecimal($other, $this->precision));
    }

    protected function _lt(BigDecimal $other)
    {
        return $this->comp($other) < 0;
    }

    public function lt($other)
    {
        return $this->_lt(new BigDecimal($other, $this->precision));
    }

    protected function _lte(BigDecimal $other)
    {
        return $this->comp($other) <= 0;
    }

    public function lte($other)
    {
        return $this->_lte(new BigDecimal($other, $this->precision));
    }

// ----------------------------------------------------------------------------

    protected function _mul(BigDecimal $other)
    {
        return new BigDecimal(bcmul($this->value, $other->value, $this->precision), $this->precision);
    }

    public function mul($other)
    {
        return $this->_mul(new BigDecimal($other, $this->precision));
    }

    protected function _div(BigDecimal $other)
    {
        return new BigDecimal(bcdiv($this->value, $other->value, $this->precision), $this->precision);
    }

    public function div($other)
    {
        return $this->_div(new BigDecimal($other, $this->precision));
    }

    protected function _mod(BigDecimal $other)
    {
        return new BigDecimal(bcmod($this->value, $other->value, $this->precision), $this->precision);
    }

    public function mod($other)
    {
        return $this->_mod(new BigDecimal($other, $this->precision));
    }

// ----------------------------------------------------------------------------

    protected function _pow(BigDecimal $other)
    {
        return new BigDecimal(bcpow($this->value, $other->value, $this->precision));
    }

    public function pow($other)
    {
        return $this->_pow(self::from($other));
    }

    public function sqrt()
    {
        return new BigDecimal(bcsqrt($this->value, $this->precision), $this->precision);
    }
}
