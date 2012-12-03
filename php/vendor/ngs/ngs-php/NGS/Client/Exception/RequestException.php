<?php
namespace NGS\Client\Exception;

class RequestException extends \Exception
{
    function __construct($response, $headers)
    {
        $this->message = $response;
    }
}
