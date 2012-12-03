<?php
namespace NGS\Client;

require_once(__DIR__.'/../Utils.php');
require_once(__DIR__.'/../Name.php');
require_once(__DIR__.'/RestHttp.php');
require_once(__DIR__.'/../Converter/PrimitiveConverter.php');
//require_once(__DIR__.'/../Patterns/DomainEvent.php');
//require_once(__DIR__.'/../Patterns/Specification.php');

use NGS\Name;
use NGS\Converter\PrimitiveConverter;
use NGS\Patterns\DomainEvent;
use NGS\Patterns\Specification;

class DomainProxy
{
    const DOMAIN_URI = 'Domain.svc';

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
            self::$instance = new DomainProxy();
        return self::$instance;
    }

    public function find($class, array $uris)
    {
        $name = Name::full($class);
        $body = array('Name' => $name, 'Uri' => PrimitiveConverter::toStringArray($uris));
        $response =
            $this->http->sendRequest(
                ApplicationProxy::APPLICATION_URI.'/GetDomainObject',
                'POST',
                json_encode($body),
                array(200));
        return RestHttp::parseResult($response, $class);
    }

    public function search($class, $limit = null, $offset = null)
    {
        $name = Name::full($class);
        $lo = RestHttp::formatLimitAndOffset($limit, $offset);
        $response =
            $this->http->sendRequest(
                self::DOMAIN_URI.'/search/'.$name.$lo,
                'GET',
                null,
                array(200));
        return RestHttp::parseResult($response, $class);
    }

    public function searchWithSpecification(
        Specification $specification,
        $limit = null,
        $offset = null)
    {
        $object = Name::parent($specification);
        $name = Name::base($specification);
        $lo = RestHttp::formatLimitAndOffset($limit, $offset);
        $response =
            $this->http->sendRequest(
                self::DOMAIN_URI.'/search/'.$object.'/'.$name.$lo,
                'POST',
                $specification->toJson(),
                array(200));
        return RestHttp::parseResult($response, Name::toClass($object));
    }

    public function count($class)
    {
        $name = Name::full($class);
        $response = $this->http->sendRequest(
            self::DOMAIN_URI.'/count/'.$name,
            'GET',
            null,
            array(200));
        $count = RestHttp::parseResult($response);
        return PrimitiveConverter::toInteger($count);
    }

    public function countWithSpecification(Specification $specification)
    {
        $object = Name::parent($specification);
        $name = Name::base($specification);
        $response =
            $this->http->sendRequest(
                self::DOMAIN_URI.'/count/'.$object.'/'.$name,
                'POST',
                $specification->toJson(),
                array(200));
        $count = RestHttp::parseResult($response);
        return PrimitiveConverter::toInteger($count);
     }

    public function submit(DomainEvent $event)
    {
        $name = Name::full($event);
        $response =
            $this->http->sendRequest(
                self::DOMAIN_URI.'/submit/'.$name,
                'POST',
                $event->toJson(),
                array(201));
        $uri = RestHttp::parseResult($response);
        return PrimitiveConverter::toString($uri);
    }
}
