<?php
namespace NGS\Client\Exception;

class SecurityException extends \Exception
{
    function __construct($response)
    {
        $this->message = $response;
    }
}
