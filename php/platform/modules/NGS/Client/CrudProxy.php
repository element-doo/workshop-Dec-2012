<?php
namespace NGS\Client;

require_once(__DIR__.'/../Utils.php');
require_once(__DIR__.'/../Name.php');
// require_once(__DIR__.'/../Patterns/AggregateRoot.php');
require_once(__DIR__.'/RestHttp.php');

use NGS\Utils;
use NGS\Name;
use NGS\Patterns\AggregateRoot;

class CrudProxy
{
    const CRUD_URI = 'Crud.svc';

    protected $http;

    protected static $instance;

    public function __construct(RestHttp $http = null)
    {
        $this->http = $http !== null ? $http : RestHttp::instance();
    }

    /**
     * Gets singleton instance of Crud.svc proxy
     * @return CrudProxy
     */
    public static function instance()
    {
        if(self::$instance === null)
            self::$instance = new CrudProxy();
        return self::$instance;
    }

    public function create(AggregateRoot $aggregate)
    {
        $class = get_class($aggregate);
        $name = Name::full($class);
        $response =
            $this->http->sendRequest(
                self::CRUD_URI.'/'.$name,
                'POST',
                $aggregate->toJson(),
                array(201));
        return RestHttp::parseResult($response, $class);
    }

    public function update(AggregateRoot $aggregate)
    {
        $class = get_class($aggregate);
        $name = Name::full($class);
        $response =
            $this->http->sendRequest(
                self::CRUD_URI.'/'.$name.'/'.rawurlencode($aggregate->getURI()),
                'PUT',
                $aggregate->toJson(),
                array(200));
        return RestHttp::parseResult($response, $class);
    }

    public function delete($class, $uri)
    {
        $name = Name::full($class);
        $response =
            $this->http->sendRequest(
                self::CRUD_URI.'/'.$name.'/'.rawurlencode($uri),
                'DELETE',
                null,
                array(200));
        return RestHttp::parseResult($response, $class);
    }

    public function read($class, $uri)
    {
        $name = Name::full($class);
        $response =
            $this->http->sendRequest(
                self::CRUD_URI.'/'.$name.'/'.rawurlencode($uri),
                'GET',
                null,
                array(200));
        return RestHttp::parseResult($response, $class);
    }
}
