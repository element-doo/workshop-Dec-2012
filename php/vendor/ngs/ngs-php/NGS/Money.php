<?php
namespace NGS;

require_once(__DIR__.'/Utils.php');
require_once(__DIR__.'/BigDecimal.php');

class Money extends \NGS\BigDecimal
{
    /**
     * @var string String representation of decimal value.
     */
    protected $value;

    protected $precision = 2;

    public function __construct($value)
    {
        if(filter_var($value, FILTER_VALIDATE_INT)) {
            $this->value = (string) $value;
        }
        else if(is_string($value) && preg_match('/^[-+]?\\d+([.]\\d\\d)?$/u', $value)) {
            $this->value = $value;
        }
        else if (($tmp = filter_var($value, FILTER_VALIDATE_FLOAT))!==false) {
            $tmp = (string) $tmp;
            if (preg_match('/^[-+]?\\d+([.]\\d\\d)?$/u', $value)) {
                $this->value = $tmp;
            }
        }
        elseif ($value instanceof \NGS\Money) {
            $this->value = $value->value;
        }
        else {
            throw new \InvalidArgumentException('Money could not be constructed from string with value "'.$value.'"');
        }
    }

    public static function toArray(array $items)
    {
        try {
            foreach ($items as $key => &$val) {
                if($val === null) {
                    throw new InvalidArgumentException('Null value found in provided array');
                }
                if(!$val instanceof \NGS\Money) {
                    $val = new \NGS\Money($val);
                }
            }
        }
        catch(\Exception $e) {
            throw new \InvalidArgumentException('Element at index '.$key.' could not be converted to Money!', 42, $e);
        }
        return $items;
    }

    public function __get($name)
    {
        if($name === 'value') {
            return $this->value;
        }
        else {
            throw new \InvalidArgumentException('Trying to get undefined property '.$name.' in NGS\\Type\\Money');
        }
    }

    public function __toString()
    {
        return $this->value;
    }
}
