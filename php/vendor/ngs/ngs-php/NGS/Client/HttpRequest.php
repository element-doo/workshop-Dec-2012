<?php
namespace NGS\Client;

// simple http client using curl
class HttpRequest
{
    private $curl;
    private $options;
    private $responseInfo;

    public function __construct($uri, $method = null, $body = null, $headers = null)
    {
        $this->curl = curl_init($uri);
        $this->options = array(
            CURLOPT_RETURNTRANSFER  => true,
            CURLINFO_HEADER_OUT     => true,
        );
        // older ngs-php versions don't use \Bootstrap
        if(class_exists('\Bootstrap'))
            $this->options[CURLOPT_CAINFO] = \Bootstrap::ensureCertPath();

        if($method !== null)
            $this->method($method);
        if($body !== null)
            $this->body($body);
        if($headers !== null)
            $this->headers($headers);
    }

    public function headers($headers)
    {
        if(!isset($this->options[CURLOPT_HTTPHEADER]))
            $this->options[CURLOPT_HTTPHEADER] = array();

        if(is_array($headers)) {
            foreach($headers as $key => $value)
                $this->options[CURLOPT_HTTPHEADER][] = $value;
        }
        else if(is_string($headers)) {
            $this->options[CURLOPT_HTTPHEADER][] = $headers;
        }
        return $this;
    }

    public function method($method)
    {
        $method = strtoupper($method);
        if ($method === 'POST') {
            $this->options[CURLOPT_POST] = true;
        } else {
            $this->options[CURLOPT_CUSTOMREQUEST] = $method;
        }
        return $this;
    }

    public function body($body)
    {
        $this->options[CURLOPT_POSTFIELDS] = $body;
    }

    public function send()
    {
        foreach ($this->options as $option => $value) {
            curl_setopt($this->curl, $option, $value);
        }
        $response = curl_exec($this->curl);
        $this->responseHeaders = curl_getinfo($this->curl);

        return $response;
    }

    public function getResponseInfo()
    {
        return $this->responseInfo;
    }

    public function getError()
    {
        $error = curl_error($this->curl);
        return $error ? $error : null;
    }
}
