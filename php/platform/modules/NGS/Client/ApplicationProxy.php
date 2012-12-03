<?php
namespace NGS\Client;

require_once(__DIR__.'/../Utils.php');
require_once(__DIR__.'/../Name.php');
require_once(__DIR__.'/RestHttp.php');

use NGS\Utils;
use NGS\Name;

class ApplicationProxy
{
    const APPLICATION_URI = 'RestApplication.svc';

    protected $http;

    protected static $instance;

    public function __construct(RestHttp $http = null)
    {
        $this->http = $http !== null ? $http : RestHttp::instance();
    }

    /**
     * Gets singleton instance of RestApplication.svc proxy
     * @return ApplicationProxy
     */
    public static function instance()
    {
        if(self::$instance === null)
            self::$instance = new ApplicationProxy();
        return self::$instance;
    }

    public function get($command, array $expectedCode = array(200), $accept = 'application/json')
    {
        return
            $this->http->sendRequest(
                self::APPLICATION_URI.'/'.$command,
                'GET',
                null,
                $expectedCode,
                $accept);
    }

    public function post(
        $command,
        array $data = null,
        array $expectedCode = array(200),
        $accept = 'application/json')
    {
        $this->postJson(
            $command,
            $data !== null ? json_encode($data) : null,
            $expectedCode,
            $accept
        );
    }

    public function postJson(
        $command,
        $data = null,
        array $expectedCode = array(200),
        $accept = 'application/json')
    {
        if(!is_string($data) && $data !== null)
            throw new \InvalidArgumentException('Data must be encoded in json string or null. Data was "'.\NGS\Utils\gettype($data).'"');
        return
            $this->http->sendRequest(
                self::APPLICATION_URI.'/'.$command,
                'POST',
                $data,
                $expectedCode,
                $accept);
    }
}
