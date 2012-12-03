<?php
namespace NGS\Client\Exception;

require_once(__DIR__.'/../../Utils.php');

use NGS\Utils;

class InvalidRequestException extends \Exception
{
    protected $response;
    protected $headers;

    function __construct($response, $headers)
    {
        $this->response = $response;
        $this->headers = $headers;
        $this->message = $this->formatMessage();
    }

    protected function formatMessage()
    {
        return $this->responseToString();
        //return sprintf('HTTP code: %s\n%s', $this->headers['http_code'], $this->responseToString());
    }

    protected function responseToString()
    {
        if(is_string($this->response)) {
            if(strpos($this->response, '<string')===0) {
                $data = new \SimpleXmlIterator($this->response);
                return (string) $data;
            }
            else {
                return $this->response;
            }
        }
        else if(is_object($this->response)) {
            if(method_exists($this->response, '__toString')) {
                return (string) $this->response;
            }
            else {
                return Utils::getType($this->response);
            }
        }
        return $this->response;
    }
}
