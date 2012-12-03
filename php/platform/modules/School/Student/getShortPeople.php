<?php
namespace School\Student;

require_once __DIR__.'/getShortPeopleJsonConverter.php';
require_once __DIR__.'/getShortPeopleArrayConverter.php';
require_once __DIR__.'/../Student.php';

/**
 * Generated from NGS DSL
 *
 * @property int $nameLimit an integer number
 *
 * @package School
 * @version 0.9.6a
 */
class getShortPeople extends \NGS\Patterns\Specification
{
    protected $nameLimit;

    /**
     * Constructs object using a key-property array or instance of class "School\Student\getShortPeople"
     *
     * @param array|void $data key-property array or instance of class "School\Student\getShortPeople" or pass void to provide all fields with defaults
     */
    public function __construct($data = array())
    {
        if (is_array($data)) {
            $this->fromArray($data);
        } else {
            throw new \InvalidArgumentException('Constructor parameter must be an array! Type was: '.\NGS\Utils::getType($data));
        }
    }

    /**
     * Supply default values for uninitialized properties
     *
     * @param array $data key-property array which will be filled in-place
     */
    private static function provideDefaults(array &$data)
    {
        if(!array_key_exists('nameLimit', $data))
            $data['nameLimit'] = 0; // an integer number
    }

    /**
     * Constructs object from a key-property array
     *
     * @param array $data key-property array
     */
    private function fromArray(array $data)
    {
        $this->provideDefaults($data);

        if (array_key_exists('nameLimit', $data))
            $this->setNameLimit($data['nameLimit']);
        unset($data['nameLimit']);

        if (count($data) !== 0 && \NGS\Utils::WarningsAsErrors())
            throw new \InvalidArgumentException('Superflous array keys found in "School\Student\getShortPeople" constructor: '.implode(', ', array_keys($data)));
    }

// ============================================================================

    /**
     * @return an integer number
     */
    public function getNameLimit()
    {
        return $this->nameLimit;
    }

    /**
     * Property getter which throws Exceptions on invalid access
     *
     * @param string $name Property name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if ($name === 'nameLimit')
            return $this->getNameLimit(); // an integer number

        throw new \InvalidArgumentException('Property "'.$name.'" in class "School\Student\getShortPeople" does not exist and could not be retrieved!');
    }

// ============================================================================

    /**
     * Property existence checker
     *
     * @param string $name Property name to check for existence
     *
     * @return bool will return true if and only if the propery exist and is not null
     */
    public function __isset($name)
    {
        if ($name === 'nameLimit')
            return true; // an integer number (always set)

        return false;
    }

    /**
     * @param int $value an integer number
     *
     * @return int
     */
    public function setNameLimit($value)
    {
        if ($value === null)
            throw new \InvalidArgumentException('Property "nameLimit" cannot be set to null because it is non-nullable!');
        $value = \NGS\Converter\PrimitiveConverter::toInteger($value);
        $this->nameLimit = $value;
        return $value;
    }

    /**
     * Property setter which checks for invalid access to specification properties and enforces proper type checks
     *
     * @param string $name Property name
     * @param mixed $value Property value
     */
    public function __set($name, $value)
    {
        if ($name === 'nameLimit')
            return $this->setNameLimit($value); // an integer number
        throw new \InvalidArgumentException('Property "'.$name.'" in class "School\Student\getShortPeople" does not exist and could not be set!');
    }

    /**
     * Will unset a property if it exists, but throw an exception if it is not nullable
     *
     * @param string $name Property name to unset (set to null)
     */
    public function __unset($name)
    {
        if ($name === 'nameLimit')
            throw new \LogicException('The property "nameLimit" cannot be unset because it is non-nullable!'); // an integer number (cannot be unset)
    }

    public function toJson()
    {
        return \School\Student\getShortPeopleJsonConverter::toJson($this);
    }

    public static function fromJson($item)
    {
        return \School\Student\getShortPeopleJsonConverter::fromJson($item);
    }

    public function __toString()
    {
        return 'School\Student\getShortPeople'.$this->toJson();
    }

    public function __clone()
    {
        return \School\Student\getShortPeopleArrayConverter::fromArray(\School\Student\getShortPeopleArrayConverter::toArray($this));
    }

    public function toArray()
    {
        return \School\Student\getShortPeopleArrayConverter::toArray($this);
    }
}