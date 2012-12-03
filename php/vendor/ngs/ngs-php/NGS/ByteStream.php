<?php
namespace NGS;

require_once(__DIR__.'/Utils.php');

use NGS\Utils;

/**
 *
 * @property string $value Returns raw value as string
 */
class ByteStream
{
    protected $value;

    /**
     * @param string|\NGS\ByteStream $value Bytestream value as string or instance of ByteStream
     */
    public function __construct($value='')
    {
        if(is_string($value)){
            $this->value = $value;
        }
        else if($value instanceof \NGS\ByteStream){
            $this->value = $value->value;
        }
        else {
            throw new \InvalidArgumentException('ByteStream could not be constructed from type "'.Utils::getType($value).'"');
        }
    }

    public static function toArray(array $items)
    {
        try {
            foreach ($items as $key => &$val) {
                if($val === null) {
                    throw new \InvalidArgumentException('Null value found in provided array');
                }
                if(!$val instanceof \NGS\ByteStream) {
                    $val = new \NGS\ByteStream($val);
                }
            }
        }
        catch(\Exception $e) {
            throw new \InvalidArgumentException('Element at index '.$key.' could not be converted to ByteStream!', 42, $e);
        }
        return $items;
    }

    public function __get($name)
    {
        if($name==='value') {
            return $this->value;
        }
        else {
            throw new \InvalidArgumentException('ByteStream: Cannot get undefined property "'.$name.'"');
        }
    }

    /**
     * Returns bytestream value encoded in base64
     */
    public function __toString()
    {
        return $this->toBase64();
    }

    /**
     * Returns bytestream value encoded in base64
     */
    public function toBase64()
    {
        return \base64_encode($this->value);
    }

    /**
     * Checks for equality against another ByteStream instance
     * @return bool
     */
    public function equals(\NGS\ByteStream $other)
    {
        return $this->value === $other->value;
    }

    /**
     * Gets bytestream size
     * @return int Bytestream size in bytes
     */
    public function size()
    {
        return strlen($this->value);
    }
}
