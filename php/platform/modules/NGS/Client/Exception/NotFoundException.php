<?php
namespace NGS\Client\Exception;

class NotFoundException extends \Exception
{
    function __construct($response)
    {
        $this->message = $response;
    }
}
