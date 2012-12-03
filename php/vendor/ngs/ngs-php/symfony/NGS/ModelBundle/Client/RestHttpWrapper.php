<?php
namespace NGS\ModelBundle\Client;

// avoid crashing if FirePHP is not installed
@include_once('FirePHPCore/fb.php');

/**
 * Wrapper around Http client, only used for logging
 */
class RestHttpWrapper extends \NGS\Client\RestHttp
{
    private $firephp;

    public function __construct($service, $username, $password)
    {
        parent::__construct($service, $username, $password);

        if(class_exists('FirePHP'))
            $this->firephp = \FirePHP::getInstance(true);
    }

    public function sendRequest(
        $uriSegment,
        $method = 'GET',
        $body = null,
        array $expectedCode = null,
        $accept = 'application/json')
    {
        if($body)
            $this->info($body, $method.' '.$uriSegment);
        else
            $this->info($method.' '.$uriSegment);

        try {
            $response = call_user_func_array('parent::sendRequest', func_get_args());
        }
        catch (\Exception $ex) {
            $msg = $ex->getMessage();
            if(isset($this->last_response['info']['http_code']))
                $msg = $this->last_response['info']['http_code'].': '.$msg;
            if($this->firephp)
                $this->firephp->warn($msg, 'NGS EXCEPTION in RestHttp::sendRequest');
            throw $ex;
        }

        if(isset($this->last_response['info']['http_code']))
            $msg = $this->last_response['info']['http_code'];
        else
            $msg = 'Response (unknown code)';
        $this->info($response, $msg);

        return $response;
    }

    private function info($content, $message=null) {
        $this->writeLog('info', $content, $message);
    }

    private function warn($content, $message=null) {
        $this->writeLog('warn', $content, $message);
    }

    private function writeLog($level, $content, $message=null)
    {
        if(!$this->firephp)
            return ;

        try {
            if(is_string($content)) {
                $jsonContent = json_decode($content);
                if($jsonContent)
                    $content = $jsonContent;
            }
            $this->firephp->$level($content, $message);
        }
        catch (\Exception $ex) {
            if(is_string($content))
                $this->firephp->$level($content, $message);
            else
                $this->firephp->error('Could not serialize log message');
        }
    }
}

?>
