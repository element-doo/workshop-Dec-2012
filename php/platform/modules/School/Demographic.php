<?php
namespace School;

require_once __DIR__.'/DemographicJsonConverter.php';
require_once __DIR__.'/DemographicArrayConverter.php';

/**
 * Generated from NGS DSL
 *
 * @property array $minors an array of objects of class "School\Student", will be calculated by server
 *
 * @package School
 * @version 0.9.6a
 */
class Demographic
{
    protected $restHttp;
    protected $minors;

    /**
     * Constructs object using a key-property array or instance of class "School\Demographic"
     *
     * @param array|void $data key-property array or instance of class "School\Demographic" or pass void to provide all fields with defaults
     */
    public function __construct($data = array(), \NGS\Client\RestHttp $restHttp = null)
    {
        if ($restHttp === null)
            $restHttp = \NGS\Client\RestHttp::instance();
        $this->restHttp = $restHttp;
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
    private static function provideDefaults(array &$data) {}

    /**
     * Constructs object from a key-property array
     *
     * @param array $data key-property array
     */
    private function fromArray(array $data)
    {
        $this->provideDefaults($data);

        if (isset($data['minors']))
            $this->minors = \School\StudentArrayConverter::fromArrayList($data['minors']);
        unset($data['minors']);

        if (count($data) !== 0 && \NGS\Utils::WarningsAsErrors())
            throw new \InvalidArgumentException('Superflous array keys found in "School\Demographic" constructor: '.implode(', ', array_keys($data)));
    }

// ============================================================================

    /**
     * @return an array of objects of class "School\Student", can be null
     */
    public function getMinors()
    {
        return $this->minors;
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
        if ($name === 'minors')
            return $this->getMinors(); // an array of objects of class "School\Student", can be null

        throw new \InvalidArgumentException('Property "'.$name.'" in class "School\Demographic" does not exist and could not be retrieved!');
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
        if ($name === 'minors')
            return true; // an array of objects of class "School\Student" (always set)

        return false;
    }

    /**
     * @param array $value an array of objects of class "School\Student"
     *
     * @return array
     */
    public function setMinors($value)
    {
        if ($value === null)
            throw new \InvalidArgumentException('Property "minors" cannot be set to null because it is non-nullable!');
        $value = \School\StudentArrayConverter::fromArrayList($value);
        $this->minors = $value;
        return $value;
    }

    /**
     * Property setter which checks for invalid access to report properties and enforces proper type checks
     *
     * @param string $name Property name
     * @param mixed $value Property value
     */
    public function __set($name, $value)
    {
        if ($name === 'minors')
            return $this->setMinors($value); // an array of objects of class "School\Student"
        throw new \InvalidArgumentException('Property "'.$name.'" in class "School\Demographic" does not exist and could not be set!');
    }

    /**
     * Will unset a property if it exists, but throw an exception if it is not nullable
     *
     * @param string $name Property name to unset (set to null)
     */
    public function __unset($name)
    {
        if ($name === 'minors')
            throw new \LogicException('The property "minors" cannot be unset because it is non-nullable!'); // an array of objects of class "School\Student" (cannot be unset)
    }

    /**
     * Populate specified report School\Demographic
     *
     * @return populated object School\Demographic
     */
    public function populate()
    {
        $proxy = new \NGS\Client\ReportingProxy($this->restHttp);
        return $proxy->populateReport($this);
    }

    /**
     * Create specified report createPdf
     *
     * @return binary object representing requested document People.xlsx populated with data and converted to pdf
     */
    public function createPdf()
    {
        $proxy = new \NGS\Client\ReportingProxy($this->restHttp);
        return $proxy->createReport($this, 'createPdf');
    }

    public function toJson()
    {
        return \School\DemographicJsonConverter::toJson($this);
    }

    public static function fromJson($item)
    {
        return \School\DemographicJsonConverter::fromJson($item);
    }

    public function __toString()
    {
        return 'School\Demographic'.$this->toJson();
    }

    public function __clone()
    {
        return \School\DemographicArrayConverter::fromArray(\School\DemographicArrayConverter::toArray($this));
    }

    public function toArray()
    {
        return \School\DemographicArrayConverter::toArray($this);
    }
}