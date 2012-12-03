<?php
namespace NGS\Client;

require_once(__DIR__.'/../Utils.php');
require_once(__DIR__.'/../Name.php');
require_once(__DIR__.'/RestHttp.php');

use NGS\Converter\PrimitiveConverter;
use NGS\Converter\ObjectConverter;
use NGS\Name;

class ReportingProxy
{
    const REPORTING_URI = 'Reporting.svc';

    protected $http;

    protected static $instance;

    public function __construct(RestHttp $http = null)
    {
        $this->http = $http !== null ? $http : RestHttp::instance();
    }

    /**
     * Gets singleton instance of Reporting.svc proxy
     * @return ReportingProxy
     */
    public static function instance()
    {
        if(self::$instance === null)
            self::$instance = new ReportingProxy();
        return self::$instance;
    }

    public function populateReport($report)
    {
        $class = get_class($report);
        $name = Name::full($report);
        $response =
        $this->http->sendRequest(
            self::REPORTING_URI.'/report/'.$name,
            'POST',
            $report->toJson(),
            array(200));
        return RestHttp::parseResult($response, $class);
    }

    public function createReport($report, $templater)
    {
        $name = Name::full($report);
        return
            $this->http->sendRequest(
                self::REPORTING_URI.'/report/'.$name.'/'.$templater,
                'POST',
                $report->toJson(),
                array(201),
                'application/octet-stream');
    }

    private static function prepareCubeCall(array $dimensions, array $facts, array $order)
    {
        $params = array();

        if($dimensions)
            $params[] = 'dimensions='.implode(',', array_map(function($a){
            return rawurlencode($a);
        }, $dimensions));
        if($facts)
            $params[] = 'facts='.implode(',', array_map(function($a){
            return rawurlencode($a);
        }, $facts));
        if($order)
            $params[] = 'order='.implode(',', array_map(function($a){
            return rawurlencode($a);
        }, $order));

        if(!$params)
            throw new \InvalidArgumentException('Cube must have at least one argument');

        return '?'.implode('&', $params);
    }

    public function olapCubeWithSpecification(
        $cube,
        $specification,
        $templater,
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
        return
            $this->http->sendRequest(
                self::REPORTING_URI.'/olap/'.$cube.'/'.$name.'/'.$templater.$arguments,
                'POST',
                $specification->toJson(),
                array(201),
                'application/octet-stream');
    }

    public function olapCube(
        $cube,
        $templater,
        array $dimensions,
        array $facts,
        array $order = array())
    {
        $cube = Name::full($cube);
        $arguments = self::prepareCubeCall($dimensions, $facts, $order);
        return
            $this->http->sendRequest(
                self::REPORTING_URI.'/olap/'.$cube.'/'.$templater.$arguments,
                'GET',
                null,
                array(201),
                'application/octet-stream');
    }

    public function getHistory($class, $uri)
    {
        return is_array($uri)
            ? $this->getCommandHistory($class, $uri)
            : $this->getRestHistory($class, $uri);
    }

    private static function parseHistoryReponse($response, $class)
    {
        $data = json_decode($response, true);
        $result = array();
        $converter = ObjectConverter::getConverter($class, ObjectConverter::ARRAY_TYPE);

        foreach($data as $dataItem)
        {
            $history = array();
            $snapshots = $dataItem['Snapshots'];
            foreach($snapshots as $snapshotItem)
            {
                $history[] =
                    new \NGS\Patterns\Snapshot(
                        $snapshotItem['At'],
                        $snapshotItem['Action'],
                        $converter::fromArray($snapshotItem['Value']));
            }
            $result[] = $history;
        }

        return $result;
    }

    private function getCommandHistory($class, $uris)
    {
        $name = Name::full($class);
        $body = array('Name' => $name, 'Uri' => PrimitiveConverter::toStringArray($uris));
        $response =
            $this->http->sendRequest(
                ApplicationProxy::APPLICATION_URI.'/GetRootHistory',
                'POST',
                json_encode($body),
                array(200));
        return self::parseHistoryResponse($response, $class);
    }

    private function getRestHistory($class, $uri)
    {
        $name = Name::full($class);
        $response =
            $this->http->sendRequest(
                self::REPORTING_URI.'/history/'.$name.'/'.$uri,
                'GET',
                null,
                array(200));
        return self::parseHistoryResponse($response, $class);
    }
}