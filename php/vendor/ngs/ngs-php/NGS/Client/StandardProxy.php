<?php
namespace NGS\Client;

require_once(__DIR__.'/../Utils.php');
require_once(__DIR__.'/../Name.php');
require_once(__DIR__.'/RestHttp.php');

use NGS\Name;

class StandardProxy
{
    const STANDARD_URI = 'Commands.svc';

    protected $http;

    protected static $instance;

    public function __construct(RestHttp $http = null)
    {
        $this->http = $http !== null ? $http : RestHttp::instance();
    }

    /**
     * Gets singleton instance of Domain.svc proxy
     * @return DomainProxy
     */
    public static function instance()
    {
        if(self::$instance === null)
            self::$instance = new StandardProxy();
        return self::$instance;
    }

    public function insert(array $aggregates)
    {
        if(empty($aggregates))
            return array();
        $response = self::persist('POST', $aggregates);
        return RestHttp::parseResult($response);
    }

    public function update(array $aggregates)
    {
        if(!empty($aggregates))
            self::persist('PUT', $aggregates);
    }

    public function delete(array $aggregates)
    {
        if(!empty($aggregates))
            self::persist('DELETE', $aggregates);
    }

    private function persist($method, array $aggregates)
    {
        $class = get_class($aggregates[0]);
        $name = Name::full($class);
        $values = array_map(function($it) { return $it->toArray(); }, $aggregates);
        return
            $this->http->sendRequest(
                self::STANDARD_URI.'/persist/'.$name,
                $method,
                json_encode($values),
                array(200));
    }

    private static function prepareCubeCall(array $dimensions, array $facts, array $order)
    {
        $params = array();

        if($dimensions)
            $params[] = 'dimensions='.implode(',', array_map(function($a){ return rawurlencode($a); }, $dimensions));
        if($facts)
            $params[] = 'facts='.implode(',', array_map(function($a){ return rawurlencode($a); }, $facts));
        if($order)
            $params[] = 'order='.implode(',', array_map(function($a){ return rawurlencode($a); }, $order));

        if(!$params)
            throw new \IlegalArgumentException('Cube must have at least one argument');

        return '?'.implode('&', $params);
    }

    public function olapCubeWithSpecification(
        $cube,
        $specification,
        array $dimensions,
        array $facts,
        array $order = array())
    {
        $cube = Name::full($cube);
        $name = Name::base($specification);
        $fullName = Name::full($specification);
        if(strncmp($fullName, $cube, strlen($cube)) != 0)
            $name = substr($fullName, 0, strlen($fullName) - strlen($name) - 1).'+'.$name;
        $arguments = self::prepareCubeCall($dimensions, $facts, $order);
        $response =
            $this->http->sendRequest(
                self::STANDARD_URI.'/olap/'.$cube.'/'.$name.$arguments,
                'POST',
                $specification->toJson(),
                array(201));
        return RestHttp::parseResult($response);
    }

    public function olapCube(
        $cube,
        array $dimensions,
        array $facts,
        array $order = array())
    {
        $cube = Name::full($cube);
        $arguments = self::prepareCubeCall($dimensions, $facts, $order);
        $response =
            $this->http->sendRequest(
                self::STANDARD_URI.'/olap/'.$cube.$arguments,
                'GET',
                null,
                array(201));
        return RestHttp::parseResult($response);
    }

    public function execute(
        $service,
        $body=null
    )
    {
        if(is_array($body))
            $body = json_encode($body);
        if(!is_string($body) && $body!==null)
            throw new \InvalidArgumentException("Execute body must be array or string");

        $response =
            $this->http->sendRequest(
                self::STANDARD_URI.'/execute/'.$service,
                'POST',
                $body,
                array(200, 201));
        return RestHttp::parseResult($response);
    }
}
