<?php
namespace NGS\Client;

require_once(__DIR__.'/../Utils.php');
require_once(__DIR__.'/../Name.php');

require_once(__DIR__.'/HttpRequest.php');
require_once(__DIR__.'/Exception/InvalidRequestException.php');
require_once(__DIR__.'/Exception/NotFoundException.php');
require_once(__DIR__.'/Exception/RequestException.php');
require_once(__DIR__.'/Exception/SecurityException.php');
require_once(__DIR__.'/../Converter/PrimitiveConverter.php');
require_once(__DIR__.'/../Converter/ObjectConverter.php');

use NGS\Client\Exception\InvalidRequestException;
use NGS\Client\Exception\NotFoundException;
use NGS\Client\Exception\RequestException;
use NGS\Client\Exception\SecurityException;
use NGS\Converter\PrimitiveConverter;
use NGS\Converter\ObjectConverter;

class RestHttp
{
    /** @var string */
    protected $service;
    protected $username;
    protected $auth;

    /** @var array */
    protected $last_response;

    /** @var RestHttp Singleton instance */
    protected static $instance;

    public function __construct($service, $username, $password)
    {
        $this->service = $service;
        $this->setAuth($username, $password);
    }

    public function setAuth($username, $password)
    {
        $this->username = $username;
        $this->auth = 'Basic '.base64_encode($username.':'.$password);
    }

    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Gets or sets singleton instance of rest Http
     * @param RestHttp
     * @return RestHttp
     */
    public static function instance(RestHttp $http = null)
    {
        if($http === null)
            return self::$instance;
        self::$instance = $http;
    }

    public static function formatLimitAndOffset($limit, $offset)
    {
        $limit = $limit === null ? null : PrimitiveConverter::toInteger($limit);
        $offset = $offset === null ? null : PrimitiveConverter::toInteger($offset);

        $args = array();

        if($limit)
            $args[] = 'limit='.$limit;
        if($offset)
            $args[] = 'offset='.$offset;

        return $args ? '?'.implode('&', $args) : '';
    }

    /**
     * Sends http request
     * @param type $uriSegment Appended to REST service uri
     * @param string $method HTTP method
     * @param type $setBody
     * @return response
     */
    public function sendRequest(
        $uriSegment,
        $method = 'GET',
        $body = null,
        array $expectedCode = null,
        $accept = 'application/json')
    {
        $request = new HttpRequest($this->service.$uriSegment, $method);
        $request->headers(array(
            'Accept: '.$accept,
            'Content-type: application/json',
            'Authorization: '.$this->auth
        ));

        if($body !== null)
            $request->body($body);

        $response = $request->send();
        $headers = $request->responseHeaders;
        $this->last_response = array('info' => $headers, 'body' => $response);

        if($response === null)
            throw new RequestException('Failed to send request', $headers);
        $httpCode = $headers['http_code'];
        if($expectedCode !== null && !in_array($httpCode, $expectedCode)) {
            switch($headers['content_type']) {
                case 'application/json':
                    $response = json_decode($response);
                    break;
                case 'text/xml':
                    $xml = new \SimpleXmlIterator($response);
                    $response = (string) $xml;
                    break;
            }
            if($httpCode < 300) {
                $errorMsg = $request->getError();
                if($errorMsg)
                    throw new RequestException($errorMsg, $headers);
                else if(trim($response))
                    throw new RequestException('Unexpected http code. Response was: '.$response, $headers);
                else
                    throw new RequestException('Unexpected http code. Response body was empty.', $headers);

            }
            switch($httpCode) {
                case 400:
                    throw new InvalidRequestException($response, $headers);
                case 401:
                case 403:
                    throw new SecurityException($response);
                case 404:
                       throw new NotFoundException($response);
                case 413:
                   throw new RequestException('Request body was too large.');
                default:
                    throw new RequestException($response, $headers);
            }
        } else {
            return $response;
        }
    }

    public static function parseResult($response, $class = null)
    {
        $data = json_decode($response, $class !== null);
        if($class !== null && is_array($data)) {
            $converter = ObjectConverter::getConverter($class);
            return $converter::fromJson($response);
        }
        return $data;
    }

    public function getLastResult()
    {
        return $this->last_response;
    }
}
